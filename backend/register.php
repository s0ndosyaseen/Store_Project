<?php
// signup.html -> register.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../signup.html');
    exit;
}

$name  = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

$errors = [];

if (empty($name)) {
    $errors[] = 'الاسم مطلوب';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'البريد الإلكتروني غير صالح';
}

if (strlen($pass) < 8) {
    $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
}

if (!empty($errors)) {
    // redirectWith('../signup.html', 'error', implode(' | ', $errors));
    $msg = implode(' | ', $errors);
    echo "<script>
        alert('$msg');
        window.location.href = '../signup.html';
    </script>";
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    // redirectWith('../signup.html', 'error', 'هذا البريد الإلكتروني مسجل مسبقاً');
    echo "<script>
        alert('هذا البريد الإلكتروني مسجل مسبقاً');
        window.location.href = '../signup.html';
    </script>";
    exit;
}

$hashedPass = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $hashedPass]);
$userId = $pdo->lastInsertId();

$sessionId = getSessionKey();
$stmt = $pdo->prepare('UPDATE cart_items SET user_id = ?, session_id = NULL WHERE session_id = ?');
$stmt->execute([$userId, $sessionId]);
/*
// تسجيل الدخول تلقائياً بعد التسجيل
$_SESSION['user_id']   = $userId;
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;

redirectWith('../home.html', 'success', 'مرحباً ' . $name . '! تم إنشاء حسابك بنجاح');
*/
$stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $hashedPass]);

echo "<script>
    alert('تم إنشاء حسابك بنجاح يا $name! يرجى تسجيل الدخول الآن.');
    window.location.href = '../login.html';
</script>";
exit;
