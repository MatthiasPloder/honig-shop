<?php
session_start();
header('Content-Type: application/json');

// Datenbankverbindung
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "honig_shop";

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES utf8");
} catch(PDOException $e) {
    echo json_encode(['error' => 'Verbindung fehlgeschlagen: ' . $e->getMessage()]);
    exit;
}

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
    $stmt = $db->prepare("
        SELECT *
        FROM orders
        WHERE order_id = ? AND user_id = ?
    ");
    
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['error' => 'Bestellung nicht gefunden']);
        exit;
    }
    
    // Bestellte Produkte abrufen
    $stmt = $db->prepare("
        SELECT 
            oi.*,
            p.productname
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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