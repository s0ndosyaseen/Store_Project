<?php
// =============================================
// دوال مساعدة عامة
// =============================================

require_once __DIR__ . '/db.php';

// تنظيف المدخلات
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// إرجاع استجابة JSON
function jsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data), JSON_UNESCAPED_UNICODE);
    exit;
}

// إعادة التوجيه مع رسالة
function redirectWith(string $url, string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    header('Location: ' . $url);
    exit;
}

// عرض رسالة Flash
function getFlash(): array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return [];
}

// الحصول على معرف الجلسة للزوار
function getSessionKey(): string {
    return session_id();
}

// التحقق من تسجيل الدخول
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

// الحصول على معلومات المستخدم الحالي
function currentUser(): array {
    if (!isLoggedIn()) return [];
    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: [];
}

// حساب إجمالي سلة المشتريات
function getCartTotal(array $cartItems): array {
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $shipping = $subtotal > 0 ? 50.00 : 0;
    $discount = $subtotal >= 3000 ? round($subtotal * 0.05, 2) : 0;
    $total = $subtotal + $shipping - $discount;
    return [
        'subtotal'      => $subtotal,
        'shipping'      => $shipping,
        'discount'      => $discount,
        'discount_pct'  => $subtotal >= 3000 ? 5 : 0,
        'total'         => $total,
    ];
}
