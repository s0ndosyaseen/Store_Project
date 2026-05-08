<?php
session_start();
setcookie("user_logged_in", "", time() - 3600, "/");
setcookie("user_email", "", time() - 3600, "/");
session_unset();
session_destroy();
echo "<script>
    alert('تم تسجيل الخروج بنجاح. نراك قريباً!');
    window.location.href = '../home.html';
</script>";
exit();