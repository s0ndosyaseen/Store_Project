<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['is_admin'])) { header('Location: index.php'); exit; }

require_once __DIR__ . '/../config/db.php';
$pdo = getDB();

$user_id = (int)($_GET['user_id'] ?? 0);
$user_name = $_GET['name'] ?? 'المستخدم';


$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$user_orders = $stmt->fetchAll();

$statusLabels = [
    'pending'   => 'قيد الانتظار',
    'confirmed' => 'مؤكد',
    'shipped'   => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي',
];
$statusColors = [
    'pending'   => '#e9d2af',
    'confirmed' => '#c7b9a6',
    'shipped'   => '#ece3aed4',
    'delivered' => '#c0cea9',
    'cancelled' => '#d8bb8f',
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات: <?= htmlspecialchars($user_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
    <style>
        .badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 11px; color: #fff; font-weight: bold; }
        .btn-detail { background: #1a1a2e; color: #fff; padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>
<header>
    <a href="users_manager.php" style="color: #c4a35a; text-decoration: none; font-size: 16px;">
        <i class="fas fa-arrow-right"></i> عودة للمستخدمين
    </a>
    <h1>سجل طلبات: <?= htmlspecialchars($user_name) ?></h1>
</header>

<div class="main">
    <div class="table-wrapper">
        <?php if (empty($user_orders)): ?>
            <div style="text-align:center; padding:50px;">
                <i class="fas fa-box-open" style="font-size: 40px; color: #ccc; margin-bottom: 10px; display: block;"></i>
                <p>هذا المستخدم لم يقم بأي طلبات بعد.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>التاريخ</th>
                    <th>المبلغ الإجمالي</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($user_orders as $order): ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td><?= date('Y/m/d', strtotime($order['created_at'])) ?></td>
                        <td><strong><?= number_format((float)($order['total'] ?? 0), 2) ?> ر.س</strong></td>
                        <td>
                            <span class="badge" style="background:<?= $statusColors[$order['status']] ?? '#999' ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="order_details.php?id=<?= $order['id'] ?>" class="btn-detail">
                                <i class="fas fa-eye"></i> التفاصيل
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>