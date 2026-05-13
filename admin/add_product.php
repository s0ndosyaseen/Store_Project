<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['is_admin'])) { header('Location: index.php'); exit; }

require_once __DIR__ . '/../config/db.php';
$pdo = getDB();
$categories = $pdo->query("SELECT slug, title FROM categories ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];
    $subcat = $_POST['subcategory'];
    $stock = $_POST['stock'];

    $targetDir = "../images/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $dbPath = "images/" . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $sql = "INSERT INTO products (name, description, price, image, category, subcategory, stock) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $desc, $price, $dbPath, $cat, $subcat, $stock]);
        header('Location: products_manager.php?msg=added');
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة منتج</title>
    <link rel="stylesheet" href="cs.css">
    <style>
        .form-container { background: #fff; padding: 30px; border-radius: 12px; max-width: 600px; margin: 20px auto; }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; }
        .btn-submit { background: #c4a35a; color: #fff; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
<div class="main">
    <div class="form-container">
        <h2>إضافة منتج جديد للمتجر</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="اسم المنتج" required>
            <textarea name="description" placeholder="وصف المنتج"></textarea>
            <input type="number" name="price" placeholder="السعر" min="0" required>
            <input type="number" name="stock" placeholder="الكمية في المخزن" min="0" required>
            <select name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['slug']) ?>">
                        <?= htmlspecialchars($category['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="subcategory" required>
                <option value="ديكور">ديكور</option>
                <option value="اكسسوارات">إكسسوارات</option>
                <option value="ملابس">ملابس</option>
            </select>
            <label>صورة المنتج:</label>
            <input type="file" name="image" required>
            <button type="submit" class="btn-submit">حفظ المنتج</button>
            <a href="products_manager.php" style="display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #7f8c8d; font-size: 14px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; transition: 0.3s; background: #fafafa;">🔙 إلغاء</a>
        </form>
    </div>
</div>
</body>
</html>
