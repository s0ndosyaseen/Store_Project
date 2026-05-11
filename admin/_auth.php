<?php
// =============================================
// مشترك: فحص صلاحية الدخول لكل صفحات الآدمن
// =============================================

if (isset($_GET['go_home'])) {
    unset($_SESSION['is_admin']);
    header('Location: ../home.html');
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$pdo = getDB();


// قراءة كلمة المرور من جدول الإعدادات (إن وُجد)
function getAdminPassword(PDO $pdo): string {
    try {
        $v = $pdo->query("SELECT setting_value FROM settings WHERE `setting_key`='admin_password' LIMIT 1")->fetchColumn();
        return $v ?: 'sonly';
    } catch (Exception $e) {
        return 'sonly';
    }
}

// تسجيل خروج
if (($_GET['logout'] ?? '') === '1') {
    unset($_SESSION['is_admin']);
    header('Location: index.php');
    exit;
}

// محاولة تسجيل دخول
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_pass'])) {
    $stored = getAdminPassword($pdo);
    $ok = false;
    if (str_starts_with($stored, '$2y$')) {
        $ok = password_verify($_POST['admin_pass'], $stored);
    } else {
        $ok = ($_POST['admin_pass'] === $stored);
    }
    if ($ok) {
        $_SESSION['is_admin'] = true;
        session_regenerate_id(true);
        header('Location: ' . basename($_SERVER['PHP_SELF']));
        exit;
    } else {
        $loginError = 'كلمة المرور غير صحيحة';
    }
}

// عرض فورم الدخول إذا لم يكن مسجلاً
if (empty($_SESSION['is_admin'])) {
    ?>
   <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>لوحة التحكم - البوصلة</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../login-signup.css">
       
    </head>
    <body>

     
    <div class="container" >
    <div class="form-section my-custom-form">

        <h2 style="color: #ffffff; background-color: #a67c37; border-radius: 20px 100px 100px 20px ; ">🔐 لوحة التحكم</h2>
         <form method="POST" >
           <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="admin_pass" placeholder="كلمة مرور الدخول" required autofocus >
      </div>
            <button type="submit" class="btn-main">تسجيل الدخول</button>

        </form>
    </div>
        <!-- <div class="overlay-content login-bg">
            <img src="../images/login.png" alt="البوصلة" class="logo" style="width:120px; height:120px;">
        </div> -->
        <?php if (!empty($loginError)): ?>
            <p class="error"><?= htmlspecialchars($loginError) ?></p>
        <?php endif; ?>
       
    </div>
    </body>
    </html>
    <?php
    exit;
}
