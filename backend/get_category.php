<?php

header('Content-Type: application/json');


require_once '../config/db.php';

try {

    $pdo = getDB();

    $type = $_GET['type'] ?? 'egypt';


    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug");
    $stmt->execute(['slug' => $type]);
    $categoryData = $stmt->fetch();

    if ($categoryData) {
        echo json_encode(['success' => true, 'data' => $categoryData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
    }

} catch (Exception $e) {

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>