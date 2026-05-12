<?php
require_once __DIR__ . '/_auth.php';

$errors = [];
$editCategory = null;
$allowedImageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

function redirectWithMessage(string $type, string $message): void {
    header('Location: add_categ.php?type=' . urlencode($type) . '&msg=' . urlencode($message));
    exit;
}

function cleanInput(string $value): string {
    return trim($value);
}

function makeSlug(string $slug): string {
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/\s+/', '-', $slug);
    return preg_replace('/[^a-z0-9_-]/', '', $slug);
}

function imageUrl(?string $path): string {
    if (!$path) {
        return '../images/logo.png';
    }

    if (preg_match('/^https?:\/\//', $path)) {
        return $path;
    }

    return '../' . ltrim($path, '/');
}

function shortText(?string $text, int $limit = 95): string {
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $limit, '...', 'UTF-8');
    }

    return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
}

function uploadCategoryImage(array $file, array $allowedImageTypes, array &$errors): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'حدث خطأ أثناء رفع الصورة.';
        return null;
    }

    if ($file['size'] > 4 * 1024 * 1024) {
        $errors[] = 'حجم الصورة يجب أن لا يتجاوز 4MB.';
        return null;
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowedImageTypes[$mime])) {
        $errors[] = 'نوع الصورة غير مدعوم. استخدم JPG أو PNG أو WEBP أو GIF.';
        return null;
    }

    $uploadDir = __DIR__ . '/../images/';
    if (!is_dir($uploadDir)) {
        $errors[] = 'مجلد الصور غير موجود.';
        return null;
    }

    $fileName = 'category_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowedImageTypes[$mime];
    $target = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $errors[] = 'تعذر حفظ الصورة داخل مجلد images.';
        return null;
    }

    return 'images/' . $fileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            redirectWithMessage('error', 'لم يتم تحديد الحضارة المطلوب حذفها.');
        }

        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        redirectWithMessage('success', 'تم حذف الحضارة بنجاح.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $slug = makeSlug($_POST['slug'] ?? '');
        $title = cleanInput($_POST['title'] ?? '');
        $heroTitle = cleanInput($_POST['hero_title'] ?? '');
        $heroDesc = cleanInput($_POST['hero_desc'] ?? '');
        $currentImage = cleanInput($_POST['current_image'] ?? '');

        if ($slug === '') {
            $errors[] = 'الاسم البرمجي مطلوب ويجب أن يحتوي على أحرف إنجليزية أو أرقام فقط.';
        }

        if ($title === '') {
            $errors[] = 'اسم الحضارة مطلوب.';
        }

        if ($heroTitle === '') {
            $heroTitle = $title;
        }

        $check = $pdo->prepare('SELECT id FROM categories WHERE slug = ? AND id <> ? LIMIT 1');
        $check->execute([$slug, $id]);
        if ($check->fetchColumn()) {
            $errors[] = 'هذا الاسم البرمجي مستخدم مسبقاً.';
        }

        $newImage = uploadCategoryImage($_FILES['bg_image'] ?? [], $allowedImageTypes, $errors);
        $imagePath = $newImage ?: $currentImage;

        if (!$errors) {
            if ($id > 0) {
                $stmt = $pdo->prepare(
                    'UPDATE categories SET slug = ?, title = ?, hero_title = ?, hero_desc = ?, bg_image = ? WHERE id = ?'
                );
                $stmt->execute([$slug, $title, $heroTitle, $heroDesc, $imagePath, $id]);
                redirectWithMessage('success', 'تم تعديل الحضارة بنجاح.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO categories (slug, title, hero_title, hero_desc, bg_image) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$slug, $title, $heroTitle, $heroDesc, $imagePath]);
            redirectWithMessage('success', 'تمت إضافة الحضارة بنجاح.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$_GET['edit']]);
    $editCategory = $stmt->fetch();
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
$message = $_GET['msg'] ?? '';
$messageType = $_GET['type'] ?? 'success';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الحضارات - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
    <style>
        .categories-page {
            width: min(1180px, calc(100% - 32px));
            margin: 28px auto 50px;
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 22px;
            align-items: start;
        }

        .panel {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(135, 103, 35, 0.18);
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(48, 39, 23, 0.10);
        }

        .panel-head {
            padding: 20px 22px 14px;
            border-bottom: 1px solid #f0e4c9;
        }

        .panel-head h2,
        .panel-head h3 {
            color: #1a1a2e;
            font-size: 18px;
            margin: 0 0 6px;
        }

        .panel-head p {
            color: #806b3a;
            font-size: 13px;
            margin: 0;
        }

        .category-form {
            padding: 20px 22px 24px;
            display: grid;
            gap: 15px;
        }

        .form-group {
            display: grid;
            gap: 7px;
        }

        .form-group label {
            color: #5d4820;
            font-weight: 700;
            font-size: 13px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border: 1px solid #dfd1b5;
            border-radius: 10px;
            background: #fffdf8;
            padding: 12px 13px;
            color: #292318;
            font-family: 'Cairo', sans-serif;
            outline: none;
            transition: 0.2s ease;
        }

        .form-group textarea {
            min-height: 108px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #b58a32;
            box-shadow: 0 0 0 4px rgba(196, 163, 90, 0.18);
            background: #fff;
        }

        .image-preview {
            width: 100%;
            height: 150px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #eadfc8;
            background: #f8f0df;
        }

        .form-actions,
        .table-actions {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 15px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: 0.2s ease;
        }

        .btn-primary {
            background: #876723;
            color: #fff;
        }

        .btn-primary:hover {
            background: #6f5318;
            transform: translateY(-1px);
        }

        .btn-soft {
            background: #f4ead8;
            color: #76561a;
        }

        .btn-edit {
            background: #e7f0ff;
            color: #1d5fa8;
        }

        .btn-delete {
            background: #fff0ed;
            color: #c0392b;
        }

        .notice {
            width: min(1180px, calc(100% - 32px));
            margin: 22px auto 0;
            padding: 13px 16px;
            border-radius: 12px;
            display: flex;
            gap: 10px;
            align-items: center;
            font-weight: 700;
        }

        .notice.success {
            background: #edf9f0;
            color: #2e7d32;
            border: 1px solid #b7e1bc;
        }

        .notice.error {
            background: #fff1f1;
            color: #c0392b;
            border: 1px solid #f1b5b0;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 0 0 14px 14px;
        }

        .category-thumb {
            width: 86px;
            height: 58px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5d8bd;
            background: #f8f0df;
        }

        .slug {
            direction: ltr;
            display: inline-block;
            color: #6d5520;
            background: #f8f0df;
            border-radius: 8px;
            padding: 4px 9px;
            font-size: 12px;
        }

        .muted {
            color: #8d805f;
            font-size: 13px;
            max-width: 330px;
        }

        .empty-state {
            padding: 34px;
            text-align: center;
            color: #8d805f;
        }

        header h1 i,
        .panel-head i,
        .btn i {
            color: inherit;
        }

        @media (max-width: 960px) {
            .categories-page {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            header {
                gap: 12px;
            }

            header h1 {
                font-size: 20px;
            }

            .categories-page {
                width: calc(100% - 20px);
                margin-top: 18px;
            }

            .form-actions .btn,
            .table-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include '_nav.php'; ?>

<header>
    <button class="open-btn" onclick="openNav()">
        <i class="fas fa-bars"></i>
    </button>
    <h1><i class="fas fa-landmark"></i> إدارة الحضارات</h1>
    <img src="../images/logo.png" alt="Logo" style="height: 60px; width: auto;">
</header>

<?php if ($message): ?>
    <div class="notice <?= $messageType === 'error' ? 'error' : 'success' ?>">
        <i class="fas fa-<?= $messageType === 'error' ? 'triangle-exclamation' : 'check-circle' ?>"></i>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="notice error">
        <i class="fas fa-triangle-exclamation"></i>
        <?= htmlspecialchars(implode(' ', $errors)) ?>
    </div>
<?php endif; ?>

<main class="categories-page">
    <section class="panel">
        <div class="panel-head">
            <h2>
                <i class="fas fa-<?= $editCategory ? 'pen-to-square' : 'plus' ?>"></i>
                <?= $editCategory ? 'تعديل الحضارة' : 'إضافة حضارة جديدة' ?>
            </h2>
            <p>املأ البيانات التي تظهر في صفحة الحضارة داخل المتجر.</p>
        </div>

        <form class="category-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string)($editCategory['id'] ?? ($_POST['id'] ?? ''))) ?>">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($editCategory['bg_image'] ?? ($_POST['current_image'] ?? '')) ?>">

            <?php if (!empty($editCategory['bg_image'])): ?>
                <img class="image-preview" src="<?= htmlspecialchars(imageUrl($editCategory['bg_image'])) ?>" alt="صورة الحضارة الحالية">
            <?php endif; ?>

            <div class="form-group">
                <label for="slug">الاسم البرمجي</label>
                <input id="slug" type="text" name="slug" dir="ltr" required placeholder="andalus"
                       value="<?= htmlspecialchars($editCategory['slug'] ?? ($_POST['slug'] ?? '')) ?>">
            </div>

            <div class="form-group">
                <label for="title">اسم الحضارة</label>
                <input id="title" type="text" name="title" required placeholder="الحضارة الأندلسية"
                       value="<?= htmlspecialchars($editCategory['title'] ?? ($_POST['title'] ?? '')) ?>">
            </div>

            <div class="form-group">
                <label for="hero_title">عنوان الواجهة</label>
                <input id="hero_title" type="text" name="hero_title" placeholder="عنوان يظهر أعلى صفحة الحضارة"
                       value="<?= htmlspecialchars($editCategory['hero_title'] ?? ($_POST['hero_title'] ?? '')) ?>">
            </div>

            <div class="form-group">
                <label for="hero_desc">وصف الواجهة</label>
                <textarea id="hero_desc" name="hero_desc" placeholder="وصف مختصر للحضارة"><?= htmlspecialchars($editCategory['hero_desc'] ?? ($_POST['hero_desc'] ?? '')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="bg_image">صورة الخلفية</label>
                <input id="bg_image" type="file" name="bg_image" accept="image/jpeg,image/png,image/webp,image/gif">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?= $editCategory ? 'حفظ التعديل' : 'إضافة الحضارة' ?>
                </button>
                <?php if ($editCategory): ?>
                    <a class="btn btn-soft" href="add_categ.php">
                        <i class="fas fa-xmark"></i>
                        إلغاء التعديل
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h3><i class="fas fa-table-list"></i> الحضارات الحالية</h3>
            <p><?= count($categories) ?> حضارة مسجلة في قاعدة البيانات.</p>
        </div>

        <div class="table-wrap">
            <?php if ($categories): ?>
                <table>
                    <thead>
                    <tr>
                        <th>الصورة</th>
                        <th>الحضارة</th>
                        <th>الاسم البرمجي</th>
                        <th>الوصف</th>
                        <th>إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <img class="category-thumb" src="<?= htmlspecialchars(imageUrl($category['bg_image'] ?? '')) ?>" alt="">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($category['title'] ?? '') ?></strong>
                                <div class="muted"><?= htmlspecialchars($category['hero_title'] ?? '') ?></div>
                            </td>
                            <td><span class="slug"><?= htmlspecialchars($category['slug'] ?? '') ?></span></td>
                            <td class="muted"><?= htmlspecialchars(shortText($category['hero_desc'] ?? '')) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-edit" href="add_categ.php?edit=<?= (int)$category['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                        تعديل
                                    </a>
                                    <form method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحضارة؟');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash"></i>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    لا توجد حضارات مضافة حتى الآن.
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
