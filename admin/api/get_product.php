<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');

// Prüfe Admin-Authentifizierung
checkAdminAuth();

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Produkt ID fehlt');
    }
    
    $product_id = intval($_GET['id']);
    
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ?
    ");
    
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        throw new Exception('Produkt nicht gefunden');
    }
    
    echo json_encode([
        'status' => 'success',
        'product' => $product
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 