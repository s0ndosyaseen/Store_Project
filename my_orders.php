<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db.php';

$pdo = getDB();
$userId = $_SESSION['user_id'] ?? null;
$orders = [];
$orderItems = [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$statusLabels = [
    'pending' => 'قيد المراجعة',
    'confirmed' => 'تم التأكيد',
    'shipped' => 'قيد الشحن',
    'delivered' => 'تم التسليم',
    'cancelled' => 'ملغي',
];

$statusIcons = [
    'pending' => 'fa-clock',
    'confirmed' => 'fa-circle-check',
    'shipped' => 'fa-truck-fast',
    'delivered' => 'fa-box-open',
    'cancelled' => 'fa-circle-xmark',
];

$steps = ['pending', 'confirmed', 'shipped', 'delivered'];

if ($userId) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    if ($orders) {
        $orderIds = array_column($orders, 'id');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT oi.*, p.image
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id IN ($placeholders)
             ORDER BY oi.id ASC"
        );
        $stmt->execute($orderIds);

        foreach ($stmt->fetchAll() as $item) {
            $orderItems[$item['order_id']][] = $item;
        }
    }
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function stepClass(string $step, string $current, array $steps): string {
    if ($current === 'cancelled') {
        return '';
    }

    $currentIndex = array_search($current, $steps, true);
    $stepIndex = array_search($step, $steps, true);

    if ($currentIndex === false || $stepIndex === false) {
        return '';
    }

    return $stepIndex <= $currentIndex ? 'done' : '';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع الطلبات - البوصلة</title>
    <link rel="stylesheet" href="shopping.css?v=4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background:
                linear-gradient(rgba(255, 250, 241, 0.88), rgba(255, 250, 241, 0.92)),
                url('images/shope_background.png');
            background-repeat: repeat;
            color: #342713;
        }

        .orders-shell {
            width: min(1180px, calc(100% - 28px));
            margin: 34px auto 70px;
        }

        .orders-hero {
            min-height: 260px;
            display: grid;
            align-items: end;
            padding: 34px;
            border-radius: 18px;
            background:
                linear-gradient(90deg, rgba(26,26,46,.82), rgba(135,103,35,.46)),
                url('images/order2.png') center/cover;
            color: #fff;
            box-shadow: 0 18px 42px rgba(46, 34, 13, .18);
            overflow: hidden;
        }

        .orders-hero h1 {
            font-size: clamp(28px, 4vw, 46px);
            margin-bottom: 10px;
        }

        .orders-hero p {
            max-width: 620px;
            line-height: 1.9;
            color: #fff2d5;
        }

        .orders-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin: 18px 0 24px;
        }

        .stat-box,
        .order-card,
        .empty-panel {
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(166,124,55,.18);
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(68, 48, 17, .08);
        }

        .stat-box {
            padding: 18px 20px;
        }

        .stat-box span {
            display: block;
            color: #8a6b2d;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .stat-box strong {
            color: #1a1a2e;
            font-size: 24px;
        }

        .orders-list {
            display: grid;
            gap: 18px;
        }

        .order-card {
            overflow: hidden;
        }

        .order-top {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 22px 24px;
            border-bottom: 1px solid #f0e5cf;
        }

        .order-title h2 {
            color: #1a1a2e;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .order-title p,
        .order-meta {
            color: #7d6b47;
            font-size: 13px;
        }

        .status-pill {
            align-self: start;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 13px;
            border-radius: 999px;
            background: #f7ecd6;
            color: #7b5819;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pill.cancelled {
            background: #fff0ed;
            color: #c0392b;
        }

        .timeline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            padding: 22px 24px 8px;
        }

        .track-step {
            position: relative;
            display: grid;
            justify-items: center;
            gap: 8px;
            color: #a39a87;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
        }

        .track-step::before {
            content: "";
            position: absolute;
            top: 17px;
            right: 50%;
            width: 100%;
            height: 3px;
            background: #eadfc8;
            z-index: 0;
        }

        .track-step:first-child::before {
            display: none;
        }

        .track-step i {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #eee4d2;
            color: #9b8a6d;
            z-index: 1;
        }

        .track-step.done {
            color: #6f5318;
        }

        .track-step.done::before,
        .track-step.done i {
            background: #c4a35a;
            color: #fff;
        }

        .cancelled-note {
            margin: 18px 24px 0;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fff0ed;
            color: #b43c2f;
            font-weight: 700;
        }

        .order-body {
            display: grid;
            grid-template-columns: 1.4fr .8fr;
            gap: 18px;
            padding: 20px 24px 24px;
        }

        .items-list {
            display: grid;
            gap: 10px;
        }

        .order-item {
            display: grid;
            grid-template-columns: 58px 1fr auto;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-radius: 12px;
            background: #fffaf1;
            border: 1px solid #f1e2c6;
        }

        .order-item img {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 10px;
            background: #f5ead5;
        }

        .order-item strong {
            color: #2f2718;
        }

        .order-item small {
            color: #806b3a;
        }

        .summary-box {
            padding: 16px;
            border-radius: 14px;
            background: #1a1a2e;
            color: #fff;
            align-self: start;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            color: #f6e8cb;
        }

        .summary-row.total {
            margin-top: 8px;
            padding-top: 13px;
            border-top: 1px solid rgba(255,255,255,.18);
            font-size: 20px;
            color: #fff;
            font-weight: 800;
        }

        .empty-panel {
            padding: 44px 24px;
            text-align: center;
            display: grid;
            justify-items: center;
            gap: 14px;
        }

        .empty-icon {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #f8ecd6;
            color: #a67c37;
            font-size: 32px;
        }

        .empty-panel h2 {
            color: #1a1a2e;
        }

        .empty-panel p {
            color: #7d6b47;
            max-width: 520px;
            line-height: 1.9;
        }

        .page-alert {
            margin: 18px 0 0;
            padding: 14px 16px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
        }

        .page-alert.success {
            background: #edf9f0;
            color: #2e7d32;
            border: 1px solid #b7e1bc;
        }

        .page-alert.error {
            background: #fff1f1;
            color: #c0392b;
            border: 1px solid #f1b5b0;
        }

        .action-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-main,
        .btn-soft {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            text-decoration: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-main {
            background: #a67c37;
            color: #fff;
        }

        .btn-soft {
            background: #f5ead5;
            color: #6f5318;
        }

        @media (max-width: 850px) {
            .orders-shell {
                width: calc(100% - 22px);
                margin: 18px auto 46px;
            }

            .orders-hero {
                min-height: 210px;
                padding: 24px 18px;
                border-radius: 14px;
            }

            .orders-stats,
            .order-body {
                grid-template-columns: 1fr;
            }

            .orders-stats {
                gap: 10px;
                margin: 14px 0 16px;
            }

            .stat-box {
                padding: 14px 16px;
            }

            .order-card {
                border-radius: 14px;
            }

            .order-top {
                flex-direction: column;
                padding: 18px;
                gap: 12px;
            }

            .status-pill {
                align-self: stretch;
                justify-content: center;
            }

            .timeline {
                grid-template-columns: repeat(4, minmax(74px, 1fr));
                gap: 6px;
                padding: 16px 12px 4px;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .timeline::-webkit-scrollbar {
                display: none;
            }

            .track-step {
                min-width: 74px;
                font-size: 11px;
                text-align: center;
                justify-items: center;
            }

            .track-step::before {
                display: block;
                top: 16px;
            }

            .track-step i {
                width: 34px;
                height: 34px;
            }

            .order-body {
                padding: 16px;
            }

            .order-item {
                grid-template-columns: 52px 1fr;
            }

            .order-item img {
                width: 52px;
                height: 52px;
            }

            .order-item > strong {
                grid-column: 2;
                justify-self: start;
                font-size: 13px;
            }

            .summary-box {
                padding: 14px;
            }
        }

        @media (max-width: 480px) {
            .orders-hero h1 {
                font-size: 28px;
            }

            .orders-hero p {
                font-size: 14px;
                line-height: 1.8;
            }

            .empty-panel {
                padding: 30px 16px;
            }

            .order-title h2 {
                font-size: 18px;
            }

            .action-row a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<header class="main-header">
    <nav>
        <ul class="navbar">
            <li class="user-menu">
                <a href="#" class="user-icon guest">
                    <i class="fas fa-chevron-down"></i>
                    <i class="fas fa-user-circle"></i>
                </a>
                <ul class="dropdown-content">
                    <li class="user-email-display">Guest@Al-Bousala.com</li>
                </ul>
            </li>
            <li><a href="home.html">الرئيسية</a></li>
            <li><a href="home.html#story">قصتنا</a></li>
            <li><a href="home.html#categories">الحضارات</a></li>
            <li><a href="cart-full.html" class="login-btn">سلة المشتريات</a></li>
            <li><a href="my_orders.php" class="login-btn">تتبع الطلب</a></li>
            <li><a href="login.html" class="login-btn login-link">تسجيل الدخول</a></li>
        </ul>
    </nav>
    <div class="logo">
        <a href="home.html">
            <img src="images/logo.png" alt="البوصلة">
        </a>
    </div>
</header>

<main class="orders-shell">
    <section class="orders-hero">
        <div>
            <h1>تتبع طلباتك</h1>
            <p>هنا تشاهدين رحلة طلبك من لحظة استلامه وحتى وصوله إليك، مع تفاصيل المنتجات والمبلغ وحالة الشحن.</p>
        </div>
    </section>

    <?php if ($flash): ?>
        <div class="page-alert <?= e($flash['type'] ?? 'success') ?>">
            <i class="fas fa-circle-info"></i>
            <?= e($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <?php if (!$userId): ?>
        <section class="empty-panel" style="margin-top:22px">
            <div class="empty-icon"><i class="fas fa-lock"></i></div>
            <h2>سجلي الدخول لعرض طلباتك</h2>
            <p>تتبع الطلبات مرتبط بحسابك، سجلي الدخول ثم ارجعي لهذه الصفحة لمتابعة حالة كل طلب.</p>
            <div class="action-row">
                <a href="login.html" class="btn-main"><i class="fas fa-right-to-bracket"></i> تسجيل الدخول</a>
                <a href="home.html#categories" class="btn-soft"><i class="fas fa-store"></i> التسوق الآن</a>
            </div>
        </section>
    <?php elseif (!$orders): ?>
        <section class="empty-panel" style="margin-top:22px">
            <div class="empty-icon"><i class="fas fa-bag-shopping"></i></div>
            <h2>لا توجد طلبات حالياً</h2>
            <p>يبدو أنك لم تقومي بأي طلب بعد. اختاري حضارة تلهمك وابدئي رحلة التسوق، وسيظهر طلبك هنا مباشرة بعد تأكيد الشراء.</p>
            <div class="action-row">
                <a href="home.html#categories" class="btn-main"><i class="fas fa-compass"></i> تسوقي الآن</a>
                <a href="cart-full.html" class="btn-soft"><i class="fas fa-cart-shopping"></i> عرض السلة</a>
            </div>
        </section>
    <?php else: ?>
        <?php
            $activeOrders = array_filter($orders, fn($order) => !in_array($order['status'], ['delivered', 'cancelled'], true));
            $latestOrder = $orders[0];
        ?>
        <section class="orders-stats">
            <div class="stat-box">
                <span>عدد الطلبات</span>
                <strong><?= count($orders) ?></strong>
            </div>
            <div class="stat-box">
                <span>طلبات قيد المتابعة</span>
                <strong><?= count($activeOrders) ?></strong>
            </div>
            <div class="stat-box">
                <span>آخر طلب</span>
                <strong>#<?= (int)$latestOrder['id'] ?></strong>
            </div>
        </section>

        <section class="orders-list">
            <?php foreach ($orders as $order): ?>
                <?php
                    $status = $order['status'] ?? 'pending';
                    $items = $orderItems[$order['id']] ?? [];
                ?>
                <article class="order-card">
                    <div class="order-top">
                        <div class="order-title">
                            <h2>طلب رقم #<?= (int)$order['id'] ?></h2>
                            <p>
                                <i class="fas fa-calendar-day"></i>
                                <?= date('Y/m/d - H:i', strtotime($order['created_at'])) ?>
                            </p>
                            <p class="order-meta">
                                <?= e($order['fname'] . ' ' . $order['lname']) ?> · <?= e($order['phone']) ?>
                            </p>
                        </div>
                        <span class="status-pill <?= $status === 'cancelled' ? 'cancelled' : '' ?>">
                            <i class="fas <?= $statusIcons[$status] ?? 'fa-clock' ?>"></i>
                            <?= $statusLabels[$status] ?? e($status) ?>
                        </span>
                    </div>

                    <?php if ($status === 'cancelled'): ?>
                        <div class="cancelled-note">
                            <i class="fas fa-circle-info"></i>
                            هذا الطلب ملغي. يمكنك التسوق من جديد في أي وقت.
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($steps as $step): ?>
                                <div class="track-step <?= stepClass($step, $status, $steps) ?>">
                                    <i class="fas <?= $statusIcons[$step] ?>"></i>
                                    <span><?= $statusLabels[$step] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="order-body">
                        <div class="items-list">
                            <?php foreach ($items as $item): ?>
                                <div class="order-item">
                                    <img src="<?= e($item['image'] ?: 'images/logo.png') ?>" alt="">
                                    <div>
                                        <strong><?= e($item['product_name']) ?></strong>
                                        <small>الكمية: <?= (int)$item['quantity'] ?></small>
                                    </div>
                                    <strong><?= number_format((float)$item['price'], 2) ?> ر.س</strong>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <aside class="summary-box">
                            <div class="summary-row">
                                <span>المجموع الفرعي</span>
                                <span><?= number_format((float)$order['subtotal'], 2) ?> ر.س</span>
                            </div>
                            <div class="summary-row">
                                <span>الشحن</span>
                                <span><?= number_format((float)$order['shipping'], 2) ?> ر.س</span>
                            </div>
                            <?php if ((float)$order['discount'] > 0): ?>
                                <div class="summary-row">
                                    <span>الخصم</span>
                                    <span>- <?= number_format((float)$order['discount'], 2) ?> ر.س</span>
                                </div>
                            <?php endif; ?>
                            <div class="summary-row total">
                                <span>الإجمالي</span>
                                <span><?= number_format((float)$order['total'], 2) ?> ر.س</span>
                            </div>
                        </aside>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

<div class="footer-bottom">
    <div class="copyright">
        <p>&copy; جميع الحقوق محفوظة لـ (Al-Bousala) البوصلة 2026</p>
    </div>
    <div class="social-icons">
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-facebook-f"></i></a>
    </div>
    <div class="bottom-links">
        <a href="#">الشروط والأحكام</a>
        <a href="#">سياسة الخصوصية</a>
    </div>
</div>

<script src="bousala.js"></script>
</body>
</html>