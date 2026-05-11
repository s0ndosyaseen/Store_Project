<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// التأكد من تسجيل الدخول كأدمن
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

// الحذف
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products_manager.php?msg=deleted');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المنتجات - البوصلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
    <style>
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .btn-add { background: #27ae60; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 8px; display: inline-block; margin-bottom: 20px; transition: 0.3s; }
        .btn-add:hover { background: #219150; }

        /* تنسيق حاوية الأزرار */
        .actions-cell { display: flex; gap: 8px; align-items: center; }

        .btn-edit {
            background: #3498db;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.3s;
        }
        .btn-edit:hover { background: #2980b9; }

        .btn-delete {
            background: #e74c3c;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-delete:hover { background: #c0392b; }

        .badge-msg { color: green; background: #d4edda; padding: 10px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
<div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="close-btn" onclick="closeNav()">&times;</a>
    <div style="text-align: center; padding: 20px;"><img src="../images/logo.png" alt="Logo" style="width: 80px;"></div>
    <a href="index.php"><i class="fas fa-chart-line"></i> الإحصائيات والطلبات</a>
    <a href="products_manager.php"><i class="fas fa-boxes"></i> إدارة المنتجات</a>
    <a href="users_manager.php"><i class="fas fa-users"></i> إدارة المستخدمين</a>
    <a href="?go_home=1"><i class="fas fa-store"></i> العودة للمتجر</a>
    <a href="?logout=1" style="color: #ff7675; border-top: 1px solid #333; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
</div>
<div id="overlay" class="overlay" onclick="closeNav()"></div>

<header>
    <button class="open-btn" onclick="openNav()"><i class="fas fa-bars"></i></button>
    <h1><i class="fas fa-box"></i> إدارة المنتجات</h1>
    <a href="add_product.php" class="btn-add"><i class="fas fa-plus"></i> إضافة منتج جديد</a>
</header>

<div class="main">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <p class="badge-msg">✅ تم حذف المنتج بنجاح!</p>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>الصورة</th>
            <th>الاسم</th>
            <th>الحضارة</th>
            <th>النوع</th>
            <th>السعر</th>
            <th>المخزون</th>
            <th>إجراءات</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="../<?= $p['image'] ?>" class="product-img"></td>
                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td><span class="badge-sub"><?= htmlspecialchars($p['subcategory']) ?></span></td>
                <td><?= number_format($p['price'], 2) ?> ر.س</td>
                <td><?= $p['stock'] ?></td>
                <td>
                    <div class="actions-cell">
                        <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="?delete=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                            <i class="fas fa-trash"></i> حذف
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    function openNav() {
        document.getElementById("mySidebar").style.width = "260px";
        document.getElementById("overlay").style.display = "block";
    }

    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
        document.getElementById("overlay").style.display = "none";
    }
</script>
</body>
</html>