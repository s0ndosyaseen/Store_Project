<?php
// =============================================
// لوحة تحكم المدير - الطلبات
// =============================================
require_once __DIR__ . '/_auth.php';

$pdo = getDB();

// تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
    if (in_array($_POST['status'], $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')
            ->execute([$_POST['status'], (int)$_POST['order_id']]);
    }
    header('Location: index.php');
    exit;
}

// فلتر الحالة
$statusFilter = $_GET['status'] ?? 'all';
$allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

if ($statusFilter !== 'all' && in_array($statusFilter, $allowed, true)) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$statusFilter]);
} else {
    $stmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
}
$orders = $stmt->fetchAll();

// إحصائيات سريعة
$stats = $pdo->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) AS confirmed,
        SUM(CASE WHEN status='shipped' THEN 1 ELSE 0 END) AS shipped,
        SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) AS delivered,
        COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END), 0) AS revenue
     FROM orders"
)->fetch();

$usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$productsCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();

$chartQuery = $pdo->query("
    SELECT 
        DATE(created_at) as order_date, 
        SUM(total) as daily_total 
    FROM orders 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
      AND status != 'cancelled'
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) ASC
");
$chartResults = $chartQuery->fetchAll(PDO::FETCH_ASSOC);

// 2. تجهيز مصفوفات للأيام والمبالغ
$days = [];
$revenues = [];

$arabicDays = [
    'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 
    'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت'
];

// ملء المصفوفات بالبيانات
foreach ($chartResults as $row) {
    $dayName = date('l', strtotime($row['order_date']));
    $days[] = $arabicDays[$dayName];
    $revenues[] = (float)$row['daily_total'];
}

// 3. تحويلها لـ JSON لاستخدامها في JavaScript
$jsDays = json_encode($days);
$jsRevenues = json_encode($revenues);


// جلب مبيعات كل قسم (حضارة)
$categoryStats = $pdo->query("
    SELECT p.category, SUM(o.total) as total_sales
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status != 'cancelled'
    GROUP BY p.category
")->fetchAll(PDO::FETCH_ASSOC);

$catLabels = [];
$catValues = [];

foreach ($categoryStats as $row) {
    // يمكنك تحويل أسماء الأقسام للعربية هنا إذا كانت مخزنة بالإنجليزي
    $catLabels[] = $row['category']; 
    $catValues[] = (float)$row['total_sales'];
}

$jsCatLabels = json_encode($catLabels);
$jsCatValues = json_encode($catValues);

$statusLabels = [
    'pending'   => 'قيد الانتظار',
    'confirmed' => 'مؤكد',
    'shipped'   => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي',
];
$statusColors = [
    'pending'   => '#e9d2af',
    'confirmed' => '#c7b9a6',
    'shipped'   => '#ece3aed4',
    'delivered' => '#c0cea9',
    'cancelled' => '#d8bb8f',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - البوصلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
</head>
<body>
<?php include '_nav.php'; ?>
<header>
    <button class="open-btn" onclick="openNav()">
        <i class="fas fa-bars"></i>
    </button>
    <h1><i class="fas fa-compass"></i> لوحة تحكم البوصلة</h1>
    <img src="../images/logo.png" alt="Logo" style="height: 60px; width: auto;">
</header>

<div class="main admin-dashboard">
    <section class="dashboard-head">
        <div>
            <span class="eyebrow">نظرة عامة</span>
            <h2>لوحة متابعة المتجر</h2>
            <p>تابع الطلبات، الإيرادات، وحركة المنتجات من مكان واحد.</p>
        </div>
        <div class="dashboard-actions">
            <a href="products_manager.php"><i class="fas fa-boxes"></i> المنتجات</a>
            <a href="add_categ.php"><i class="fas fa-landmark"></i> الحضارات</a>
        </div>
    </section>
    <!-- إحصائيات -->
    <div class="stats">
        <div class="stat-card">
            <div class="num"><?= $stats['total'] ?></div>
            <div class="lbl">إجمالي الطلبات</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:#f39c12"><?= $stats['pending'] ?></div>
            <div class="lbl">قيد الانتظار</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:#27ae60"><?= $stats['delivered'] ?></div>
            <div class="lbl">تم التوصيل</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $usersCount ?></div>
            <div class="lbl">المستخدمون</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $productsCount ?></div>
            <div class="lbl">المنتجات</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:#c4a35a; font-size:20px"><?= number_format((float)$stats['revenue'], 0) ?> ر.س</div>
            <div class="lbl">إجمالي الإيرادات</div>
        </div>
    </div>

    <!-- قسم الرسم البياني -->
 <div class="dashboard-charts">
    <div class="chart-container">
        <h3 style="margin-bottom: 15px;">تحليلات المبيعات (آخر 7 أيام)</h3>
         <canvas id="salesChart" height="100"></canvas>
    </div>
    <!-- الرسم الدائري للأقسام -->
    <div class="chart-container">
        <h3><i class="fas fa-chart-pie"></i> مبيعات الحضارات</h3>
        <canvas id="categoryChart"></canvas>
    </div>

</div>
<!-- استدعاء مكتبة الرسم البياني -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');

// استقبال البيانات من PHP
const labelsData = <?php echo $jsDays; ?>;
const revenuesData = <?php echo $jsRevenues; ?>;

const salesChart = new Chart(ctx, {
    type: 'line', 
    data: {
        labels: labelsData, // الأيام الحقيقية من الداتابيز
        datasets: [{
            label: 'إجمالي المبيعات (ر.س)',
            data: revenuesData, // المبالغ الحقيقية من الداتابيز
            borderColor: '#c4a35a',
            backgroundColor: 'rgba(196, 163, 90, 0.1)',
            borderWidth: 3,
            pointBackgroundColor: '#1a1a2e',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                rtl: true,
                bodyFont: { family: 'Cairo' },
                titleFont: { family: 'Cairo' }
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                position: 'right',
                ticks: { font: { family: 'Cairo' } }
            },
            x: { 
                ticks: { font: { family: 'Cairo' } }
            }
        }
    }
});


const catCtx = document.getElementById('categoryChart').getContext('2d');

const categoryChart = new Chart(catCtx, {
    type: 'pie', // أو 'doughnut' لشكل أكثر حداثة
    data: {
        labels: <?php echo $jsCatLabels; ?>,
        datasets: [{
            data: <?php echo $jsCatValues; ?>,
            backgroundColor: [
                '#c4a35a', // اللون الذهبي الأساسي
                '#1a1a2e', // الكحلي الداكن
                '#e9d2af', // البيج
                '#c7b9a6', // البرونزي
                '#a67c37'  // البني النحاسي
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { family: 'Cairo', size: 12 } }
            },
            tooltip: {
                rtl: true,
                bodyFont: { family: 'Cairo' }
            }
        }
    }
});
</script>

    <!-- فلاتر الحالة -->
    <div class="filters">
        <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">الكل (<?= $stats['total'] ?>)</a>
        <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">قيد الانتظار (<?= $stats['pending'] ?>)</a>
        <a href="?status=confirmed" class="<?= $statusFilter === 'confirmed' ? 'active' : '' ?>">مؤكدة (<?= $stats['confirmed'] ?>)</a>
        <a href="?status=shipped" class="<?= $statusFilter === 'shipped' ? 'active' : '' ?>">تم الشحن (<?= $stats['shipped'] ?>)</a>
        <a href="?status=delivered" class="<?= $statusFilter === 'delivered' ? 'active' : '' ?>">تم التوصيل (<?= $stats['delivered'] ?>)</a>
    </div>

    <!-- جدول الطلبات -->
    <section class="orders-panel">
        <div class="orders-panel-head">
            <div>
                <h3><i class="fas fa-receipt"></i> الطلبات الأخيرة</h3>
                <p>إدارة حالات الطلبات ومراجعة تفاصيل العملاء.</p>
            </div>
        </div>
    <?php if (empty($orders)): ?>
        <div class="empty"><i class="fas fa-box-open" style="font-size:40px;margin-bottom:12px;display:block"></i>لا توجد طلبات</div>
    <?php else: ?>
    <div class="admin-table-scroll">
    <table class="orders-table">
        <thead>
            <tr>
                <th>#</th>
                <th>العميل</th>
                <th>البريد / الهاتف</th>
                <th>العنوان</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td data-label="#"><strong>#<?= $order['id'] ?></strong></td>
                <td data-label="العميل"><?= htmlspecialchars($order['fname'] . ' ' . $order['lname']) ?></td>
                <td data-label="التواصل">
                    <?= htmlspecialchars($order['email']) ?><br>
                    <small><?= htmlspecialchars($order['phone']) ?></small>
                </td>
                <td data-label="العنوان" style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <?= htmlspecialchars($order['address']) ?>
                </td>
                <td data-label="الإجمالي"><strong><?= number_format((float)$order['total'], 2) ?> ر.س</strong></td>
                <td data-label="الحالة">
                    <span class="badge" style="background:<?= $statusColors[$order['status']] ?>">
                        <?= $statusLabels[$order['status']] ?>
                    </span>
                </td>
                <td data-label="التاريخ"><?= date('Y/m/d', strtotime($order['created_at'])) ?></td>
                <td data-label="إجراء">
                    <form method="POST" style="display:flex;gap:6px;align-items:center">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status">
                            <?php foreach ($statusLabels as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>>
                                    <?= $lbl ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-save">حفظ</button>
                        <a href="order_details.php?id=<?= $order['id'] ?>" class="btn-details">تفاصيل</a>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
    </section>
</div>

</body>
</html>
