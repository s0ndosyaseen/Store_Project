<?php
// تأكد من عدم وجود أي فراغات أو أسطر قبل علامة <?php
header('Content-Type: application/json');

// تأكد أن المسار هنا صحيح لملف الـ config الذي أرسلته لي
require_once '../config/db.php'; 

try {
    // الحصول على اتصال PDO من الدالة الموجودة في ملف db.php
    $pdo = getDB(); 
    
    $type = $_GET['type'] ?? 'egypt';

    // استخدام طريقة PDO في الاستعلام
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug");
    $stmt->execute(['slug' => $type]);
    $categoryData = $stmt->fetch();

    if ($categoryData) {
        echo json_encode(['success' => true, 'data' => $categoryData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
    }

} catch (Exception $e) {
    // في حال حدث خطأ، نرسله بصيغة JSON وليس HTML
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>