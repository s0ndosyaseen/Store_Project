<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'المستخدم غير مسجل دخول'], JSON_UNESCAPED_UNICODE);
    exit;
}

$user = currentUser();

if ($user) {
    echo json_encode(['success' => true, 'user' => $user], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل في جلب بيانات المستخدم'], JSON_UNESCAPED_UNICODE);
}