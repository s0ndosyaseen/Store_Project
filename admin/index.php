<?php
// =============================================
// لوحة تحكم المدير - الطلبات
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

// حماية لوحة التحكم بكلمة مرور بسيطة
// غيّر هذه القيمة لكلمة مرور حقيقية
define('ADMIN_PASS', 'sonly');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_pass'])) {
    if ($_POST['admin_pass'] === ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
    } else {
        $loginError = 'كلمة المرور غير صحيحة';
    }
}

if ($_GET['logout'] ?? false) {
    unset($_SESSION['is_admin']);
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['is_admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>لوحة التحكم - البوصلة</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #1a1a2e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .login-box { background: #fff; padding: 40px; border-radius: 12px; width: 340px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
            .login-box h2 { margin-bottom: 24px; color: #1a1a2e; }
            .login-box input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; margin-bottom: 16px; box-sizing: border-box; }
            .login-box button { width: 100%; padding: 12px; background: #c4a35a; color: #fff; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; }
            .error { color: red; margin-bottom: 12px; font-size: 14px; }
        </style>
    </head>
    <body>
    <div class="login-box">
        <h2>🔐 لوحة التحكم</h2>
        <?php if (!empty($loginError)): ?>
            <p class="error"><?= htmlspecialchars($loginError) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="admin_pass" placeholder="كلمة مرور الدخول" required autofocus>
            <button type="submit">دخول</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$pdo = getDB();

// تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
    if (in_array($_POST['status'], $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')
            ->execute([$_POST['status'], (int)$_POST['order_id']]);
    }
    header('Location: index.php');
    exit;
}

// فلتر الحالة
$statusFilter = $_GET['status'] ?? 'all';
$allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

if ($statusFilter !== 'all' && in_array($statusFilter, $allowed, true)) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$statusFilter]);
} else {
    $stmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
}
$orders = $stmt->fetchAll();

// إحصائيات سريعة
$stats = $pdo->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) AS confirmed,
        SUM(CASE WHEN status='shipped' THEN 1 ELSE 0 END) AS shipped,
        SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) AS delivered,
        COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END), 0) AS revenue
     FROM orders"
)->fetch();

$usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$productsCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();

$statusLabels = [
    'pending'   => 'قيد الانتظار',
    'confirmed' => 'مؤكد',
    'shipped'   => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي',
];
$statusColors = [
    'pending'   => '#f39c12',
    'confirmed' => '#3498db',
    'shipped'   => '#9b59b6',
    'delivered' => '#27ae60',
    'cancelled' => '#e74c3c',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - البوصلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f5f5f0; color: #333; direction: rtl; }
        header { background: #1a1a2e; color: #c4a35a; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; }
        header h1 { font-size: 20px; }
        header a { color: #fff; text-decoration: none; font-size: 14px; opacity: .7; }
        header a:hover { opacity: 1; }
        .main { padding: 24px 32px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .stat-card .num { font-size: 28px; font-weight: bold; color: #1a1a2e; }
        .stat-card .lbl { font-size: 13px; color: #888; margin-top: 4px; }
        .filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .filters a { padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 13px; background: #eee; color: #555; }
        .filters a.active, .filters a:hover { background: #1a1a2e; color: #fff; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        th { background: #1a1a2e; color: #c4a35a; padding: 12px 14px; text-align: right; font-size: 13px; font-weight: 600; }
        td { padding: 11px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafaf7; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; color: #fff; }
        select { border: 1px solid #ddd; border-radius: 6px; padding: 5px 10px; font-size: 12px; cursor: pointer; background: #fff; }
        .btn-save { background: #c4a35a; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; }
        .btn-details { background: #1a1a2e; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .empty { text-align: center; padding: 40px; color: #aaa; }
    </style>
</head>
<body>
<header>
    <h1><i class="fas fa-compass"></i> لوحة تحكم البوصلة</h1>
    <a href="?logout=1"><i class="fas fa-sign-out-alt"></i> خروج</a>
</header>

<div class="main">
    <!-- إحصائيات -->
    <div class="stats">
        <div class="stat-card">
            <div class="num"><?= $stats['total'] ?></div>
            <div class="lbl">إجمالي الطلبات</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:#f39c12"><?= $stats['pending'] ?></div>
            <div class="lbl">قيد الانتظار</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:#27ae60"><?= $stats['delivered'] ?></div>
            <div class="lbl">تم التوصيل</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $usersCount ?></div>
            <div class="lbl">المستخدمون</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $productsCount ?></div>
            <div class="lbl">المنتجات</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:#c4a35a; font-size:20px"><?= number_format((float)$stats['revenue'], 0) ?> ر.س</div>
            <div class="lbl">إجمالي الإيرادات</div>
        </div>
    </div>

    <!-- فلاتر الحالة -->
    <div class="filters">
        <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">الكل (<?= $stats['total'] ?>)</a>
        <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">قيد الانتظار (<?= $stats['pending'] ?>)</a>
        <a href="?status=confirmed" class="<?= $statusFilter === 'confirmed' ? 'active' : '' ?>">مؤكدة (<?= $stats['confirmed'] ?>)</a>
        <a href="?status=shipped" class="<?= $statusFilter === 'shipped' ? 'active' : '' ?>">تم الشحن (<?= $stats['shipped'] ?>)</a>
        <a href="?status=delivered" class="<?= $statusFilter === 'delivered' ? 'active' : '' ?>">تم التوصيل (<?= $stats['delivered'] ?>)</a>
    </div>

    <!-- جدول الطلبات -->
    <?php if (empty($orders)): ?>
        <div class="empty"><i class="fas fa-box-open" style="font-size:40px;margin-bottom:12px;display:block"></i>لا توجد طلبات</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>العميل</th>
                <th>البريد / الهاتف</th>
                <th>العنوان</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong>#<?= $order['id'] ?></strong></td>
                <td><?= htmlspecialchars($order['fname'] . ' ' . $order['lname']) ?></td>
                <td>
                    <?= htmlspecialchars($order['email']) ?><br>
                    <small><?= htmlspecialchars($order['phone']) ?></small>
                </td>
                <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <?= htmlspecialchars($order['address']) ?>
                </td>
                <td><strong><?= number_format((float)$order['total'], 2) ?> ر.س</strong></td>
                <td>
                    <span class="badge" style="background:<?= $statusColors[$order['status']] ?>">
                        <?= $statusLabels[$order['status']] ?>
                    </span>
                </td>
                <td><?= date('Y/m/d', strtotime($order['created_at'])) ?></td>
                <td>
                    <form method="POST" style="display:flex;gap:6px;align-items:center">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status">
                            <?php foreach ($statusLabels as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>>
                                    <?= $lbl ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-save">حفظ</button>
                        <a href="order_details.php?id=<?= $order['id'] ?>" class="btn-details">تفاصيل</a>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
