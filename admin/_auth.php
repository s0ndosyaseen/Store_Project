<?php




if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_GET['go_home'])) {
    unset($_SESSION['is_admin'], $_SESSION['user_role']);
    header('Location: ../home.html');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$pdo = getDB();


if (($_GET['logout'] ?? '') === '1') {
    unset($_SESSION['is_admin'], $_SESSION['user_role']);
    header('Location: index.php');
    exit;
}


$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_pass'])) {
    $inputPass = $_POST['admin_pass'];

    $stmt = $pdo->query("SELECT * FROM accounts");
    $allAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $foundAccount = null;
    foreach ($allAccounts as $acc) {
        $stored = $acc['password'];
        $ok = false;

        if (strpos($stored, '$2y$') === 0) {
            $ok = password_verify($inputPass, $stored);
        } else {
            $ok = ($inputPass === $stored);
        }

        if ($ok) {
            $foundAccount = $acc;
            break;
        }
    }

    if ($foundAccount) {
        $_SESSION['is_admin'] = true;
        $_SESSION['user_role'] = $foundAccount['role'];
        session_regenerate_id(true);
        header('Location: ' . basename($_SERVER['PHP_SELF']));
        exit;
    } else {
        $loginError = 'كلمة المرور غير صحيحة';
    }
}


if (empty($_SESSION['is_admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>لوحة التحكم - البوصلة</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../login-signup.css">
        <style>
            .error-toast {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #ff4d4d;
                color: white;
                padding: 10px 25px;
                border-radius: 30px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                z-index: 1000;
                font-weight: bold;
            }
        </style>
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

        <?php if (!empty($loginError)): ?>
            <div class="error-toast">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($loginError) ?>
            </div>

            <script>
                setTimeout(() => {
                    const toast = document.querySelector('.error-toast');
                    if(toast) toast.style.display = 'none';
                }, 4000);
            </script>
        <?php endif; ?>

    </div>
    </body>
    </html>
    <?php
    exit;
}