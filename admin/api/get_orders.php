<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    $query = "SELECT o.*, u.email as user_email 
              FROM orders o 
              LEFT JOIN users u ON o.user_id = u.user_id
              WHERE 1=1";
    $params = [];
    
    if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
        $query .= " AND o.order_status = ?";
        $params[] = $_GET['order_status'];
    }
    
    if (isset($_GET['payment_status']) && !empty($_GET['payment_status'])) {
        $query .= " AND o.payment_status = ?";
        $params[] = $_GET['payment_status'];
    }
    
    $query .= " ORDER BY o.order_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'orders' => $orders
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler beim Laden der Bestellungen: ' . $e->getMessage()
    ]);
}
?> 