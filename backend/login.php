<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
$pdo = getDB();

if (isset($_SESSION['user_id'])) {
    header("Location: ../home.html");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        redirectWith('../login.html', 'error', 'يرجى إدخال البريد الإلكتروني وكلمة المرور');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $old_session_id = session_id();
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // ربط السلة
        try {
            $updateCart = $pdo->prepare("UPDATE cart_items SET user_id = ?, session_id = NULL WHERE session_id = ?");
            $updateCart->execute([$user['id'], $old_session_id]);
        } catch (Exception $e) {}

        setcookie("user_logged_in", "true", time() + 3600, "/");
        setcookie("user_email", $user['email'], time() + 3600, "/");
        echo "<script>
            alert('تم تسجيل الدخول بنجاح! مرحباً بك يا " . $user['name'] . "');
            window.location.href = '../home.html';
          </script>";
        exit();

    } else {
        // --- حالة الخطأ ---
        echo "<script>
            alert('البريد الإلكتروني أو كلمة المرور غير صحيحة!');
            window.location.href = '../login.html';
          </script>";
        exit();
    }
}