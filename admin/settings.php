<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/db.php';
$pdo = getDB();// لتعريف المتغير بسطر28

// منع الموظف من دخول هذه الصفحة نهائياً
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$pageTitle = 'الإعدادات العامة';

// ============================================================
// تأكد من وجود جدول settings
// ============================================================

$defaults = [
    //'admin_password' => 'sonly', مش لازمة لانه حذفت الباسوورد من السيتنج
    'store_name'     => 'البوصلة',
    'store_phone'    => '',
    'store_email'    => '',
    'store_whatsapp' => '',
    'store_address'  => '',
    'footer_text'    => 'استكشف جمال الحضارات العالمية من خلال قطعنا الفريدة والمختارة بعناية.',
];

foreach ($defaults as $k => $v) {
    $pdo->prepare(
        'INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)'
    )->execute([$k, $v]);
}

$msg = '';
$msgType = 'success';

// ============================================================
// معالجة POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- تغيير كلمة المرور ---
    if ($action === 'change_password') {
        $current = $_POST['current_pass'] ?? '';
        $new1    = $_POST['new_pass']     ?? '';
        $new2    = $_POST['confirm_pass'] ?? '';

        // الباسورد الحالي للأدمن من جدول accounts
        $stmt = $pdo->prepare("SELECT password FROM accounts WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $stored = $stmt->fetchColumn();

        $ok = false;
        if (strpos($stored, '$2y$') === 0) {
            $ok = password_verify($current, $stored);
        } else {
            $ok = ($current === $stored);
        }

        if (!$ok) {
            $msg = 'كلمة المرور الحالية غير صحيحة';
            $msgType = 'error';
        } elseif (strlen($new1) < 6) {
            $msg = 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل';
            $msgType = 'error';
        } elseif ($new1 !== $new2) {
            $msg = 'كلمتا المرور الجديدتان غير متطابقتين';
            $msgType = 'error';
        } else {
            $hashed = password_hash($new1, PASSWORD_DEFAULT);
            // التحديث يتم في جدول accounts للأدمن
            $pdo->prepare("UPDATE accounts SET password=? WHERE role='admin'")
                    ->execute([$hashed]);
            $msg = 'تم تغيير كلمة مرور الإدارة بنجاح ✓';
        }
    }
    // --- تحديث معلومات المتجر ---
    elseif ($action === 'update_store') {
        $fields = ['store_name','store_phone','store_email','store_whatsapp','store_address','footer_text'];
        $upd = $pdo->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?");
        foreach ($fields as $f) {
            $upd->execute([trim($_POST[$f] ?? ''), $f]);
        }
        $msg = 'تم حفظ إعدادات المتجر بنجاح ✓';
    }

    header('Location: settings.php?msg=' . urlencode($msg) . '&type=' . $msgType);
    exit;
}


if (!$msg && isset($_GET['msg'])) {
    $msg     = $_GET['msg'];
    $msgType = $_GET['type'] ?? 'success';
}

// جلب كل الإعدادات
$rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
$settings = [];
foreach ($rows as $r) $settings[$r['setting_key']] = $r['setting_value'];

/*  رسائل التواصل */

$messages = $pdo->query("
    SELECT *
    FROM contact_messages
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - البوصلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
    <style>
        .sidebar {
    height: 100%;
    width: 0; /* يبدأ بعرض صفر ليكون مخفياً */
    position: fixed; /* ثابت فوق كل شيء */
    z-index: 2000; /* قيمة عالية جداً لضمان ظهوره فوق الهيدر */
    top: 0;
    right: 0; /* لأنه موقع عربي */
    background-color: #1a1a2e; /* نفس لون الهيدر للفخامة */
    overflow-x: hidden;
    transition: 0.5s; /* سرعة الحركة */
    padding-top: 60px;
    box-shadow: -2px 0 10px rgba(0,0,0,0.5);
}

/* تنسيق الروابط داخل السايد بار */
.sidebar a {
    padding: 15px 25px;
    text-decoration: none;
    font-size: 16px;
    color: #c4a35a; /* اللون الذهبي */
    display: block;
    transition: 0.3s;
    text-align: right;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.sidebar a:hover {
    background-color: #252545;
    color: #fff;
}

/* زر الإغلاق */
.sidebar .close-btn {
    position: absolute;
    top: 10px;
    left: 25px; /* في جهة اليسار لأن السايد بار على اليمين */
    font-size: 36px;
    border: none;
}

/* طبقة التعتيم الخلفية */
.overlay {
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(0,0,0,0.7);
    z-index: 1500; /* أقل من السايد بار وأعلى من محتوى الصفحة */

}
/* =========================
   SETTINGS PAGE DESIGN
========================= */

.form-card {
    background: rgba(255,255,255,0.95);
    border-radius: 18px;
    padding: 28px;
    /* margin-right: 24px;
    margin-left: 24px; */
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(196,163,90,0.25);
    backdrop-filter: blur(4px);
    transition: 0.3s ease;
}

.form-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.12);
}

/* العناوين */
.form-card h3 {
    color: #1a1a2e;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 22px;
    border-bottom: 1px solid rgba(196,163,90,0.2);
    padding-bottom: 12px;
}

/* الفورم */
.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #6d5520;
}

/* الانبوتات */
.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 13px 15px;
    border-radius: 12px;
    border: 1px solid #ddd;
    background: #fffdf8;
    font-family: 'Cairo', sans-serif;
    font-size: 14px;
    color: #444;
    transition: 0.3s ease;
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 90px;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #c4a35a;
    box-shadow: 0 0 0 4px rgba(196,163,90,0.15);
    background: #fff;
}

/* زرار العين */
.form-group button {
    transition: 0.3s;
}

.form-group button:hover {
    color: #c4a35a !important;
}

/* الأزرار */
.btn {
    border: none;
    border-radius: 12px;
    padding: 13px 22px;
    cursor: pointer;
    font-family: 'Cairo', sans-serif;
    font-size: 15px;
    font-weight: 700;
    transition: 0.3s ease;
}

.btn-gold {
    background: linear-gradient(135deg, #d4b16a, #b58a32);
    color: white;
    box-shadow: 0 4px 12px rgba(181,138,50,0.25);
}

.btn-gold:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(181,138,50,0.35);
}

/* رسائل التنبيه */
.alert {
    width: 90%;
    margin: 20px auto;
    padding: 15px 18px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.alert-success {
    background: #edf9f0;
    color: #2e7d32;
    border: 1px solid #b7e1bc;
}

.alert-error {
    background: #fff1f1;
    color: #c0392b;
    border: 1px solid #f1b5b0;
}

/* system info */
.system-box {
    background: white;
    border-radius: 12px;
    padding: 14px;
    border: 1px solid #eee;
    transition: 0.3s ease;
}

.system-box:hover {
    border-color: #c4a35a;
    transform: translateY(-2px);
}

.system-box .label {
    font-size: 12px;
    color: #999;
    margin-bottom: 5px;
}

.system-box .value {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a2e;
    font-family: monospace;
}

/* شبكة الكروت */
.settings-grid {
    padding: 24px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-top: 30px;
}


/* ========================= */
/* رسائل التواصل */
/* ========================= */

.messages-container{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.message-card{
    background:#fffdf9;
    border:1px solid rgba(196,163,90,0.2);
    border-radius:16px;
    padding:18px;
    transition:0.3s;
}

.message-card:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 18px rgba(62, 51, 28, 0.06);
}

.message-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:12px;
    gap:20px;
}

.message-top h4{
    color:#1a1a2e;
    margin-bottom:4px;
    font-size:16px;
}

.message-top span{
    color: rgba(194, 159, 83, 0.9);
    font-size:13px;
}

.message-top small{
    color: rgba(62, 51, 28, 0.9);
    font-size:12px;
    white-space:nowrap;
}

.message-text{
    color: rgba(62, 51, 28, 0.9);
    line-height:1.8;
    font-size:14px;
}

.empty-msg{
    text-align:center;
    padding:30px;
    color:#999;
    font-size:14px;
}





/* responsive */
@media (max-width: 900px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {

    .form-card {
        padding: 20px;
    }

    .form-group input,
    .form-group textarea {
        font-size: 13px;
    }

    .btn {
        width: 100%;
    }

    .alert {
        width: 95%;
        font-size: 13px;
    }
}

/* حركة ناعمة */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-card,
.alert {
    animation: fadeUp 0.4s ease;
}

    </style>

</head>
<body>

<?php include '_nav.php'; ?>
<header>
    <button class="open-btn" onclick="openNav()">
        <i class="fas fa-bars"></i>
    </button>
    <h1><i class="fas fa-compass"></i> الاعدادات </h1>
    <img src="../images/logo.png" alt="Logo" style="height: 60px; width: auto;">
</header>


<?php if ($msg): ?>
<div class="alert alert-<?= $msgType==='success'?'success':'error' ?>">
    <i class="fas fa-<?= $msgType==='success'?'check-circle':'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div class="settings-grid">

    <!-- ===== تغيير كلمة المرور ===== -->
    <div class="form-card">
        <h3 style="color:#1a1a2e;font-size:16px;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #f0f0f0">
            <i class="fas fa-lock" style="color:#c4a35a"></i> تغيير كلمة مرور الآدمن
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group" style="margin-bottom:14px">
                <label>كلمة المرور الحالية</label>
                <div style="position:relative">
                    <input type="password" name="current_pass" required id="cur" style="padding-left:40px">
                    <button type="button" onclick="togglePass('cur')"
                            style="position:absolute;left:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#aaa">
                        <i class="fas fa-eye" id="curIcon"></i>
                    </button>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label>كلمة المرور الجديدة (6 أحرف على الأقل)</label>
                <div style="position:relative">
                    <input type="password" name="new_pass" required id="new1" minlength="6" style="padding-left:40px">
                    <button type="button" onclick="togglePass('new1')"
                            style="position:absolute;left:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#aaa">
                        <i class="fas fa-eye" id="new1Icon"></i>
                    </button>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label>تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="confirm_pass" required minlength="6">
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%">
                <i class="fas fa-save"></i> تغيير كلمة المرور
            </button>
        </form>

        <div style="margin-top:18px;padding:14px;background:#fef9ec;border-radius:8px;font-size:12px;color:#888;border:1px solid #f0d98e">
            <i class="fas fa-triangle-exclamation" style="color:#f39c12"></i>
            <strong>تنبيه أمني:</strong> تأكد من حفظ كلمة المرور الجديدة في مكان آمن.
            إذا نسيتها، ستحتاج لتعديلها من قاعدة البيانات.
        </div>
    </div>

    <!-- ===== معلومات المتجر ===== -->
    <div class="form-card">
        <h3 style="color:#1a1a2e;font-size:16px;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #f0f0f0">
            <i class="fas fa-store" style="color:#c4a35a"></i> معلومات المتجر والتواصل
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_store">
            <div class="form-group" style="margin-bottom:14px">
                <label>اسم المتجر</label>
                <input type="text" name="store_name" value="<?= htmlspecialchars($settings['store_name'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label><i class="fas fa-phone"></i> رقم الهاتف</label>
                <input type="text" name="store_phone" value="<?= htmlspecialchars($settings['store_phone'] ?? '') ?>" placeholder="+970 50 000 0000" dir="ltr">
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                <input type="email" name="store_email" value="<?= htmlspecialchars($settings['store_email'] ?? '') ?>" placeholder="info@albousala.com" dir="ltr">
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label><i class="fab fa-whatsapp" style="color:#27ae60"></i> رقم واتساب</label>
                <input type="text" name="store_whatsapp" value="<?= htmlspecialchars($settings['store_whatsapp'] ?? '') ?>" placeholder="+966 50 000 0000" dir="ltr">
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label><i class="fas fa-location-dot"></i> العنوان</label>
                <input type="text" name="store_address" value="<?= htmlspecialchars($settings['store_address'] ?? '') ?>" placeholder="المدينة، المملكة العربية السعودية">
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label><i class="fas fa-align-right"></i> نص الفوتر</label>
                <textarea name="footer_text" style="min-height:70px"><?= htmlspecialchars($settings['footer_text'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%">
                <i class="fas fa-save"></i> حفظ الإعدادات
            </button>
        </form>
    </div>
        </div>

</div>


<!-- رسائل التواصل -->

<div class="form-card" style="margin-top:24px">

    <h3 style="
        color:#1a1a2e;
        font-size:18px;
        margin-bottom:20px;
        border-bottom:1px solid #eee;
        padding-bottom:12px;
    ">
        <i class="fas fa-envelope"></i>
        رسائل التواصل
    </h3>

    <?php if(count($messages) > 0): ?>

        <div class="messages-container">

            <?php foreach($messages as $msg): ?>

                <div class="message-card">

                    <div class="message-top">

                        <div>
                            <h4>
                                <?= htmlspecialchars($msg['name']) ?>
                            </h4>

                            <span>
                                <?= htmlspecialchars($msg['email']) ?>
                            </span>
                        </div>

                        <small>
                            <?= date('Y-m-d H:i', strtotime($msg['created_at'])) ?>
                        </small>

                    </div>

                    <p class="message-text">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty-msg">
            لا توجد رسائل حالياً
        </div>

    <?php endif; ?>

</div>



<script>
function togglePass(id) {
    const input = document.getElementById(id);
    const icon  = document.getElementById(id + 'Icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
</body>
</html>
