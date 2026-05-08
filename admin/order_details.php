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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f5f5f0; color: #333; direction: rtl; }
        header { background: #1a1a2e; color: #c4a35a; padding: 16px 32px; display: flex; align-items: center; gap: 16px; }
        header a { color: #fff; opacity:.7; text-decoration: none; font-size: 14px; }
        header h1 { font-size: 18px; }
        .main { padding: 28px 32px; max-width: 900px; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .card h3 { margin-bottom: 16px; color: #1a1a2e; font-size: 16px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .info-item label { font-size: 12px; color: #888; display: block; margin-bottom: 3px; }
        .info-item span { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: right; padding: 10px; font-size: 13px; border-bottom: 2px solid #f0f0f0; color: #666; font-weight: 600; }
        td { padding: 10px; font-size: 13px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        td img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
        .totals { text-align: left; margin-top: 12px; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .totals .total-row { font-weight: bold; font-size: 16px; border-top: 2px solid #1a1a2e; margin-top: 6px; padding-top: 10px; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 12px; font-size: 12px; color: #fff; background: #f39c12; }
    </style>
</head>
<body>
<header>
    <a href="index.php"><i class="fas fa-arrow-right"></i> العودة</a>
    <h1>تفاصيل الطلب #<?= $orderId ?></h1>
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
