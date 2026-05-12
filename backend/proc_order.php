<?php
// =============================================
// معالجة الطلب / إتمام الشراء
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../checkout.html');
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$userId) {
    $message = 'يجب تسجيل الدخول أولاً لإكمال الشراء';
    if ($isAjax) {
        jsonResponse(false, $message);
    } else {
        redirectWith('../login.html', 'error', $message);
    }
}

$pdo = getDB();

$stmt = $pdo->prepare(
    'SELECT ci.id AS cart_item_id, ci.quantity,
            p.id AS product_id, p.name, p.price, p.stock
     FROM cart_items ci JOIN products p ON p.id = ci.product_id
     WHERE ci.user_id = ?'
);
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    if ($isAjax) {
        jsonResponse(false, 'سلة المشتريات فارغة');
    } else {
        redirectWith('../cart-empty.html', 'error', 'سلة المشتريات فارغة');
    }
}

foreach ($cartItems as $item) {
    if ($item['quantity'] > $item['stock']) {
        $message = 'المنتج "' . $item['name'] . '" لا يتوفر بالكمية المطلوبة';
        if ($isAjax) {
            jsonResponse(false, $message);
        } else {
            redirectWith('../cart-full.html', 'error', $message);
        }
    }
}

$fname = sanitize($_POST['fname'] ?? '');
$lname = sanitize($_POST['lname'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$address = sanitize($_POST['address'] ?? '');

if (empty($email)) {
    $userInfo = currentUser();
    $email = $userInfo['email'] ?? '';
}

$errors = [];
if (empty($fname))   $errors[] = 'الاسم الأول مطلوب';
if (empty($lname))   $errors[] = 'الاسم الأخير مطلوب';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
if (empty($phone))   $errors[] = 'رقم الهاتف مطلوب';
if (empty($address)) $errors[] = 'عنوان التوصيل مطلوب';

if (!empty($errors)) {
    $message = implode(' | ', $errors);
    if ($isAjax) {
        jsonResponse(false, $message);
    } else {
        redirectWith('../checkout.html', 'error', $message);
    }
}

$totals = getCartTotal($cartItems);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        'INSERT INTO orders (user_id, fname, lname, email, phone, address, subtotal, shipping, discount, total)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $fname,
        $lname,
        $email,
        $phone,
        $address,
        $totals['subtotal'],
        $totals['shipping'],
        $totals['discount'],
        $totals['total'],
    ]);
    $orderId = $pdo->lastInsertId();

    $insertItem = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)'
    );
    $updateStock = $pdo->prepare(
        'UPDATE products SET stock = stock - ? WHERE id = ?'
    );

    foreach ($cartItems as $item) {
        $insertItem->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
        ]);
        $updateStock->execute([$item['quantity'], $item['product_id']]);
    }

    $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?')->execute([$userId]);

    $pdo->commit();

    $_SESSION['last_order_id'] = $orderId;
    $_SESSION['last_order_total'] = $totals['total'];

    $successMessage = 'شكراً ' . $fname . '! تم استلام طلبك رقم #' . $orderId . ' بنجاح. سيتم التواصل معك لتأكيد الشحن.';

    if ($isAjax) {
        jsonResponse(true, $successMessage, [
            'order_id' => $orderId,
            'redirect_url' => 'my_orders.php'
        ]);
    } else {
        redirectWith('../my_orders.php', 'success', $successMessage);
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $errorMessage = 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage();
    if ($isAjax) {
        jsonResponse(false, $errorMessage);
    } else {
        redirectWith('../checkout.html', 'error', $errorMessage);
    }
}
