<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تنظيف المدخلات
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        echo "<script>
                alert('يرجى إدخال البريد الإلكتروني');
                window.location.href = '../forgot-password.html';
              </script>";
        exit();
    }

    // البحث عن الإيميل في قاعدة البيانات
    $stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "<script>
                alert('أهلاً " . $user['name'] . "، تم العثور على حسابك. سيتم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني قريباً.');
                window.location.href = '../login.html';
              </script>";
    } else {
        echo "<script>
                alert('لم يتم العثور على البريد الإلكتروني، يرجى إنشاء حساب جديد');
                window.location.href = '../signup.html';
              </script>";
    }
    exit();
}