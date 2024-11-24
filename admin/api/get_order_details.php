<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Bestell-ID fehlt');
    }

    $order_id = intval($_GET['id']);

    // Bestellinformationen abrufen
    $stmt = $pdo->prepare("
        SELECT o.*, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Bestellung nicht gefunden');
    }

    // Bestellte Produkte abrufen
    $stmt = $pdo->prepare("
        SELECT oi.*, p.productname, p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'order' => $order,
        'items' => $items
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 