<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

// Überprüfen ob Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Nicht autorisiert']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Keine Bestellnummer angegeben']);
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'];

try {
    // Bestelldetails abrufen
    $stmt = $pdo->prepare("
        SELECT *
        FROM orders
        WHERE order_id = ? AND user_id = ?
    ");
    
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['error' => 'Bestellung nicht gefunden']);
        exit;
    }
    
    // Bestellte Produkte abrufen
    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            p.productname
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
    
    $result = [
        'order_id' => $order['order_id'],
        'order_date' => $order['order_date'],
        'total_price' => $order['total_price'],
        'shipping_address' => $order['shipping_address'],
        'billing_address' => $order['billing_address'],
        'order_status' => $order['order_status'],
        'payment_status' => $order['payment_status'],
        'items' => $items
    ];
    
    echo json_encode($result);

} catch(PDOException $e) {
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?> 