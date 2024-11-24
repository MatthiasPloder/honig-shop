<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

// Überprüfen ob Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Nicht autorisiert']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            o.total_price,
            o.order_status,
            o.payment_status,
            COUNT(oi.order_item_id) as items_count
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.order_id, o.order_date, o.total_price, o.order_status, o.payment_status
        ORDER BY o.order_date DESC
    ");
    
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    
    echo json_encode($orders);

} catch(PDOException $e) {
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?> 