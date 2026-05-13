<?php




if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$pdo       = getDB();
$userId    = $_SESSION['user_id'] ?? null;
$sessionId = getSessionKey();
$action    = $_GET['action'] ?? ($_POST['action'] ?? '');


function fetchCart(PDO $pdo, ?int $userId, string $sessionId): array {
    if ($userId) {
        $stmt = $pdo->prepare(
            'SELECT ci.id, ci.quantity, p.id AS product_id, p.name, p.price, p.image, p.stock
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.user_id = ?'
        );
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->prepare(
            'SELECT ci.id, ci.quantity, p.id AS product_id, p.name, p.price, p.image, p.stock
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.session_id = ?'
        );
        $stmt->execute([$sessionId]);
    }
    return $stmt->fetchAll();
}

switch ($action) {


    case 'view':
        $items = fetchCart($pdo, $userId, $sessionId);
        $totals = getCartTotal($items);
        echo json_encode([
            'success' => true,
            'items'   => $items,
            'count'   => count($items),
            'totals'  => $totals,
        ], JSON_UNESCAPED_UNICODE);
        break;


    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int)($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'معرف المنتج غير صالح'], JSON_UNESCAPED_UNICODE);
            break;
        }


        $stmt = $pdo->prepare('SELECT id, stock FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'المنتج غير موجود'], JSON_UNESCAPED_UNICODE);
            break;
        }


        if ($userId) {
            $stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$userId, $productId]);
        } else {
            $stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?');
            $stmt->execute([$sessionId, $productId]);
        }
        $existing = $stmt->fetch();

        $newQty = $existing ? $existing['quantity'] + $quantity : $quantity;

        if ($newQty > $product['stock']) {
            echo json_encode(['success' => false, 'message' => 'الكمية المطلوبة تتجاوز المخزون المتاح'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if ($existing) {
            $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?')
                ->execute([$newQty, $existing['id']]);
        } else {
            if ($userId) {
                $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)')
                    ->execute([$userId, $productId, $quantity]);
            } else {
                $pdo->prepare('INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)')
                    ->execute([$sessionId, $productId, $quantity]);
            }
        }

        $items = fetchCart($pdo, $userId, $sessionId);
        echo json_encode([
            'success' => true,
            'message' => 'تمت إضافة المنتج إلى السلة',
            'count'   => count($items),
        ], JSON_UNESCAPED_UNICODE);
        break;


    case 'update':
        $itemId   = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'message' => 'معرف غير صالح'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if ($quantity <= 0) {

            $pdo->prepare('DELETE FROM cart_items WHERE id = ?')->execute([$itemId]);
            $items = fetchCart($pdo, $userId, $sessionId);
            $totals = getCartTotal($items);
            echo json_encode([
                'success' => true,
                'message' => 'تم حذف المنتج من السلة',
                'items'   => $items,
                'count'   => count($items),
                'totals'  => $totals,
            ], JSON_UNESCAPED_UNICODE);
            break;
        }


        $stmt = $pdo->prepare(
            'SELECT ci.id, p.stock FROM cart_items ci JOIN products p ON p.id = ci.product_id WHERE ci.id = ?'
        );
        $stmt->execute([$itemId]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'العنصر غير موجود'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if ($quantity > $row['stock']) {
            echo json_encode(['success' => false, 'message' => 'الكمية المطلوبة تتجاوز المخزون'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?')->execute([$quantity, $itemId]);
        $items = fetchCart($pdo, $userId, $sessionId);
        $totals = getCartTotal($items);
        echo json_encode([
            'success' => true,
            'message' => 'تم تحديث الكمية',
            'items'   => $items,
            'count'   => count($items),
            'totals'  => $totals,
        ], JSON_UNESCAPED_UNICODE);
        break;


    case 'remove':
        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'message' => 'معرف غير صالح'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $pdo->prepare('DELETE FROM cart_items WHERE id = ?')->execute([$itemId]);
        $items = fetchCart($pdo, $userId, $sessionId);
        $totals = getCartTotal($items);
        echo json_encode([
            'success' => true,
            'message' => 'تم حذف المنتج',
            'items'   => $items,
            'count'   => count($items),
            'totals'  => $totals,
        ], JSON_UNESCAPED_UNICODE);
        break;


    case 'clear':
        if ($userId) {
            $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?')->execute([$userId]);
        } else {
            $pdo->prepare('DELETE FROM cart_items WHERE session_id = ?')->execute([$sessionId]);
        }
        echo json_encode(['success' => true, 'message' => 'تم تفريغ السلة', 'count' => 0], JSON_UNESCAPED_UNICODE);
        break;


    case 'count':
        if ($userId) {
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) AS cnt FROM cart_items WHERE user_id = ?');
            $stmt->execute([$userId]);
        } else {
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) AS cnt FROM cart_items WHERE session_id = ?');
            $stmt->execute([$sessionId]);
        }
        $row = $stmt->fetch();
        echo json_encode(['success' => true, 'count' => (int)$row['cnt']], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'طلب غير معروف'], JSON_UNESCAPED_UNICODE);
}