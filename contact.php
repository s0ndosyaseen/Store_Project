<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pdo = getDB();

/* جلب الإعدادات */
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");

$settings = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $settings[$row['setting_key']] = $row['setting_value'];
}

$msg = '';
 if($_SERVER['REQUEST_METHOD'] === 'POST'){ 
    $name = sanitize($_POST['name'] ?? '');
  $email = sanitize($_POST['email'] ?? ''); 
  $message = sanitize($_POST['message'] ?? ''); 
  if($name && $email && $message){ 
    $insert = $pdo->prepare(" INSERT INTO contact_messages(name,email,message) VALUES(?,?,?) ");
     $insert->execute([$name,$email,$message]); $msg = "تم إرسال رسالتك بنجاح ✓"; } }
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تواصل معنا - <?= htmlspecialchars($settings['store_name']) ?></title>

    <link rel="stylesheet" href="contact.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<section class="contact-section">

    <div class="overlay"></div>

    <div class="contact-container">

        <div class="contact-header">
            <h1>تواصل معنا</h1>
            <p>
                نحن هنا لمساعدتك والإجابة على جميع استفساراتك
            </p>
        </div>

        <div class="contact-grid">

            <!-- معلومات التواصل -->
            <div class="contact-info">

                <div class="info-card">
                    <i class="fas fa-store"></i>

                    <div>
                        <h3>اسم المتجر</h3>
                        <p><?= htmlspecialchars($settings['store_name']) ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <i class="fas fa-phone"></i>

                    <div>
                        <h3>رقم الهاتف</h3>
                        <p><?= htmlspecialchars($settings['store_phone']) ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <i class="fas fa-envelope"></i>

                    <div>
                        <h3>البريد الإلكتروني</h3>
                        <p><?= htmlspecialchars($settings['store_email']) ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <i class="fab fa-whatsapp"></i>

                    <div>
                        <h3>واتساب</h3>
                        <p><?= htmlspecialchars($settings['store_whatsapp']) ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <i class="fas fa-location-dot"></i>

                    <div>
                        <h3>العنوان</h3>
                        <p><?= htmlspecialchars($settings['store_address']) ?></p>
                    </div>
                </div>

            </div>

            <!-- الفورم -->
            <div class="contact-form-box">

                <h2>أرسل رسالة</h2>

      <form method="POST">
    
      <?php if(!empty($msg)): ?>
           <div class="success-msg">
               <?= $msg ?>
           </div>
       <?php endif; ?>
        
       <div class="form-group">
           <input type="text"
                 name="name"
                  placeholder="الاسم الكامل"
                 required>
      </div>
        
      <div class="form-group">
           <input type="email"
                  name="email"
                  placeholder="البريد الإلكتروني"
                  required>
       </div>
        
       <div class="form-group">
           <textarea name="message"
                     placeholder="اكتب رسالتك هنا..."
                     required></textarea>
       </div>
        
       <button type="submit">
           <i class="fas fa-paper-plane"></i>
             إرسال الرسالة
      </button>
        
    </form>

            </div>

        </div>

    </div>

</section>

</body>
</html>
