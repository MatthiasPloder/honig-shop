<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');

checkAdminAuth();

try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'categories' => $categories
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler beim Laden der Kategorien: ' . $e->getMessage()
    ]);
}
?> 