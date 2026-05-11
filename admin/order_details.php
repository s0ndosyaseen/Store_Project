<?php
// =============================================
// تفاصيل طلب محدد - لوحة التحكم
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$pdo = getDB();
$orderId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die('<p style="text-align:center;padding:40px">الطلب غير موجود</p>');
}

$stmt = $pdo->prepare(
    'SELECT oi.*, p.image FROM order_items oi
     LEFT JOIN products p ON p.id = oi.product_id
     WHERE oi.order_id = ?'
);
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

$statusLabels = [
    'pending'   => 'قيد الانتظار',
    'confirmed' => 'مؤكد',
    'shipped'   => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الطلب #<?= $orderId ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="order.css">
   
</head>
<body>

<?php include '_nav.php'; ?>

<header>
     <button class="open-btn" onclick="openNav()" style="margin-left: 30px;">
        <i class="fas fa-bars"></i>
    </button>
  
    <a href="javascript:history.back()" >
        <i class="fas fa-arrow-right"></i> عودة
    </a>
    <h1>تفاصيل الطلب #<?= $orderId ?></h1>
    <button onclick="window.print()" class="btn-print">
        <i class="fas fa-print"></i> طباعة الفاتورة
    </button>
</header>
<div class="main">
    <!-- بيانات العميل -->
    <div class="card">
        <h3><i class="fas fa-user"></i> بيانات العميل</h3>
        <div class="info-grid">
            <div class="info-item"><label>الاسم</label><span><?= htmlspecialchars($order['fname'] . ' ' . $order['lname']) ?></span></div>
            <div class="info-item"><label>البريد الإلكتروني</label><span><?= htmlspecialchars($order['email']) ?></span></div>
            <div class="info-item"><label>رقم الهاتف</label><span><?= htmlspecialchars($order['phone']) ?></span></div>
            <div class="info-item"><label>حالة الطلب</label><span class="badge"><?= $statusLabels[$order['status']] ?></span></div>
            <div class="info-item" style="grid-column: 1 / -1"><label>عنوان التوصيل</label><span><?= htmlspecialchars($order['address']) ?></span></div>
        </div>
    </div>

    <!-- المنتجات -->
    <div class="card">
        <h3><i class="fas fa-box"></i> المنتجات</h3>
        <table>
            <thead>
                <tr>
                    <th>صورة</th>
                    <th>اسم المنتج</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php if ($item['image']): ?><img src="../<?= htmlspecialchars($item['image']) ?>" alt=""><?php endif; ?></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= number_format((float)$item['price'], 2) ?> ر.س</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2) ?> ر.س</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">
            <div><span>المجموع الفرعي:</span><span><?= number_format((float)$order['subtotal'], 2) ?> ر.س</span></div>
            <div><span>رسوم الشحن:</span><span><?= number_format((float)$order['shipping'], 2) ?> ر.س</span></div>
            <?php if ($order['discount'] > 0): ?>
            <div style="color:#27ae60"><span>خصم:</span><span>- <?= number_format((float)$order['discount'], 2) ?> ر.س</span></div>
            <?php endif; ?>
            <div class="total-row"><span>الإجمالي:</span><span><?= number_format((float)$order['total'], 2) ?> ر.س</span></div>
        </div>
    </div>
</div>
</body>
</html>
