<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Nicht eingeloggt');
    }

    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT 
            sc.cart_id,
            sc.quantity,
            p.product_id,
            p.productname,
            p.price,
            p.image_url
        FROM shopping_cart sc
        JOIN products p ON sc.product_id = p.product_id
        WHERE sc.user_id = ?
    ");
    
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'items' => $cartItems
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 