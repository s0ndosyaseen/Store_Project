<div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="close-btn" onclick="closeNav()">&times;</a>
    <div style="text-align: center; padding: 20px;">
        <img src="../images/logo.png" alt="Logo" style="width: 80px;">
    </div>
    <a href="index.php"><i class="fas fa-chart-line"></i> الإحصائيات والطلبات</a>
    <a href="products_manager.php"><i class="fas fa-boxes"></i> إدارة المنتجات</a>
    <a href="users_manager.php"><i class="fas fa-users"></i> إدارة المستخدمين</a>
    <a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a>
    <a href="?go_home=1"><i class="fas fa-store"></i> العودة للمتجر</a>
    <a href="?logout=1" style="color: #ff7675; border-top: 1px solid #333; margin-top: 20px;">
        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
    </a>
</div>

<!-- طبقة التعتيم عند فتح القائمة -->
<div id="overlay" class="overlay" onclick="closeNav()"></div>

<!--للسايدر -->
<script>
    function openNav() {
        document.getElementById("mySidebar").style.width = "260px";
        document.getElementById("overlay").style.display = "block";
    }

    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
        document.getElementById("overlay").style.display = "none";
    }
</script>