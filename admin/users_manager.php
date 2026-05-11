<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}
if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    header('Location: index.php');
    exit;
}

if (isset($_GET['go_home'])) {
    unset($_SESSION['is_admin']);
    header('Location: ../home.html');
    exit;
}

require_once __DIR__ . '/../config/db.php';
$pdo = getDB();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: users_manager.php?msg=deleted');
    exit;
}

$users = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المستخدمين - البوصلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
    <style>
        .user-icon { width: 40px; height: 40px; background: #e4c175; color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 18px; }
        .msg-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .table-wrapper { background: #fff; border-radius: 12px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        .btn-view-orders { background: #27ae60; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .btn-view-orders:hover { background: #219150; }

        .btn-delete-user { background: #e74c3c; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .btn-delete-user:hover { background: #c0392b; }
    </style>
</head>
<body>
<?php include '_nav.php'; ?>

<header>
    <button class="open-btn" onclick="openNav()"><i class="fas fa-bars"></i></button>
    <h1><i class="fas fa-users"></i> إدارة المستخدمين</h1>
    <img src="../images/logo.png" alt="Logo" style="height: 60px; width: auto;">
</header>

<div class="main">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="msg-success">✅ تم حذف المستخدم بنجاح من النظام.</div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>رقم التعريف</th>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>تاريخ التسجيل</th>
                <th>إجراءات</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?= $user['id'] ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-icon"><i class="fas fa-user"></i></div>
                            <strong><?= htmlspecialchars($user['name']) ?></strong>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('Y/m/d', strtotime($user['created_at'])) ?></td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="user_orders.php?user_id=<?= $user['id'] ?>&name=<?= urlencode($user['name']) ?>" class="btn-view-orders">
                                <i class="fas fa-shopping-bag"></i> عرض الطلبات
                            </a>
                            <a href="?delete=<?= $user['id'] ?>" class="btn-delete-user" onclick="return confirm('هل أنتِ متأكدة؟ سيتم حذف حساب المستخدم نهائياً.')">
                                <i class="fas fa-trash"></i> حذف الحساب
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>