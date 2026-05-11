<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['is_admin'])) { header('Location: index.php'); exit; }
require_once __DIR__ . '/../config/db.php';
$pdo = getDB();

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die("المنتج غير موجود");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];
    $subcat = $_POST['subcategory'];
    $stock = $_POST['stock'];
    $imagePath = $product['image'];

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../images/";
        $fileName = basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $fileName);
        $imagePath = "images/" . $fileName;
    }

    $sql = "UPDATE products SET name=?, description=?, price=?, image=?, category=?, subcategory=?, stock=? WHERE id=?";
    $pdo->prepare($sql)->execute([$name, $desc, $price, $imagePath, $cat, $subcat, $stock, $id]);
    header('Location: products_manager.php?msg=updated');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل المنتج</title>
    <link rel="stylesheet" href="cs.css">
    <style>
        .form-container { background: #fff; padding: 30px; border-radius: 12px; max-width: 600px; margin: 40px auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-submit { background: #c4a35a; color: #fff; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 16px; }
        .current-img { width: 100px; height: 100px; object-fit: cover; margin: 10px 0; border-radius: 8px; }
    </style>
</head>
<body>
<div class="main">
    <div class="form-container">
        <h2>تعديل منتج: <?= htmlspecialchars($product['name']) ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            <input type="number" name="price" min="0" value="<?= $product['price'] ?>" required>
            <input type="number" name="stock" min="0" value="<?= $product['stock'] ?>">

            <select name="category">
                <option value="andalus" <?= $product['category'] == 'andalus' ? 'selected' : '' ?>>أندلسية</option>
                <option value="sham" <?= $product['category'] == 'sham' ? 'selected' : '' ?>>بلاد الشام</option>
                <option value="victory" <?= $product['category'] == 'victory' ? 'selected' : '' ?>>فيكتورية</option>
                <option value="egypt" <?= $product['category'] == 'egypt' ? 'selected' : '' ?>>فرعونية</option>
            </select>

            <select name="subcategory">
                <option value="ديكور" <?= $product['subcategory'] == 'ديكور' ? 'selected' : '' ?>>ديكور</option>
                <option value="اكسسوارات" <?= $product['subcategory'] == 'اكسسوارات' ? 'selected' : '' ?>>إكسسوارات</option>
                <option value="ملابس" <?= $product['subcategory'] == 'ملابس' ? 'selected' : '' ?>>ملابس</option>
            </select>

            <p>الصورة الحالية:</p>
            <img src="../<?= $product['image'] ?>" class="current-img">
            <input type="file" name="image">
            <small>(اتركه فارغاً إذا كنت لا تريد تغيير الصورة)</small>

            <button type="submit" class="btn-submit" style="margin-top:10px">تحديث البيانات</button>
            <br><br>
            <a href="products_manager.php" style="display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #95a5a6; font-size: 14px; padding: 12px; border-radius: 8px; background: #fdfdfd; border: 1px dashed #ccc; transition: all 0.3s;">❌ إلغاء التعديلات والعودة</a>        </form>
    </div>
</div>
</body>
</html>