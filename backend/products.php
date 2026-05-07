<?php
// =============================================
// API: جلب المنتجات حسب الحضارة
// =============================================

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

    // قائمة المنتجات (مع فلترة حسب الحضارة)
    case 'list':
        $allowed = ['andalus', 'sham', 'victory', 'egypt'];
        if (!empty($category) && in_array($category, $allowed, true)) {
            $stmt = $pdo->prepare(
                'SELECT id, name, description, price, image, category, stock
                 FROM products WHERE category = ? ORDER BY id ASC'
            );
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query(
                'SELECT id, name, description, price, image, category, stock
                 FROM products ORDER BY category, id ASC'
            );
        }
        $products = $stmt->fetchAll();
        echo json_encode(['success' => true, 'products' => $products], JSON_UNESCAPED_UNICODE);
        break;

    // تفاصيل منتج واحد
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

    // بحث في المنتجات
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
