<?php




if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$pdo = getDB();

$action   = $_GET['action'] ?? 'list';
$category = $_GET['category'] ?? '';
$id       = (int)($_GET['id'] ?? 0);

switch ($action) {


    case 'list':
        $subcategory = $_GET['subcategory'] ?? '';
        $sort = $_GET['sort'] ?? '';

        if ($sort === 'bestselling') {
            $sql = "
                SELECT p.id, p.name, p.description, p.price, p.image, p.category, p.subcategory, p.stock,
                       COALESCE(s.sold_qty, 0) AS sold_count
                FROM products p
                LEFT JOIN (
                    SELECT oi.product_id, SUM(oi.quantity) AS sold_qty
                    FROM order_items oi
                    JOIN orders o ON o.id = oi.order_id
                    WHERE o.status != 'cancelled'
                    GROUP BY oi.product_id
                ) s ON s.product_id = p.id
                WHERE 1=1";
        } else {
            $sql = 'SELECT id, name, description, price, image, category, subcategory, stock FROM products WHERE 1=1';
        }
        $params = [];

        if (!empty($category)) {
            $sql .= $sort === 'bestselling' ? ' AND p.category = ?' : ' AND category = ?';
            $params[] = $category;
        }

        if (!empty($subcategory)) {
            $sql .= $sort === 'bestselling' ? ' AND p.subcategory = ?' : ' AND subcategory = ?';
            $params[] = $subcategory;
        }

        $sql .= $sort === 'bestselling'
            ? ' ORDER BY sold_count DESC, p.id ASC'
            : ' ORDER BY id ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        echo json_encode(['success' => true, 'products' => $products], JSON_UNESCAPED_UNICODE);
        break;


    case 'single':
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'معرف المنتج غير صالح'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $stmt = $pdo->prepare(
            'SELECT id, name, description, price, image, category, stock FROM products WHERE id = ?'
        );
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'المنتج غير موجود'], JSON_UNESCAPED_UNICODE);
            break;
        }
        echo json_encode(['success' => true, 'product' => $product], JSON_UNESCAPED_UNICODE);
        break;


    case 'search':
        $query = sanitize($_GET['q'] ?? '');
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'يرجى إدخال كلمة للبحث'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $stmt = $pdo->prepare(
            'SELECT id, name, description, price, image, category, stock
             FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY id ASC'
        );
        $like = '%' . $query . '%';
        $stmt->execute([$like, $like]);
        $products = $stmt->fetchAll();
        echo json_encode(['success' => true, 'products' => $products], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'طلب غير معروف'], JSON_UNESCAPED_UNICODE);
}