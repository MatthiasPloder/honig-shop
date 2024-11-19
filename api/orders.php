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

$user_id = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("
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
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($orders);

} catch(PDOException $e) {
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?> 