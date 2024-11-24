<?php
session_start();
require_once('../config/database.php');  // Neue zentrale Datenbankverbindung

header('Content-Type: application/json');

// POST-Daten empfangen
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Keine gültigen Daten empfangen']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Bestellung erstellen
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            order_date, 
            total_price, 
            shipping_address, 
            billing_address, 
            order_status, 
            payment_status, 
            user_id
        ) VALUES (
            NOW(), 
            ?, 
            ?, 
            ?, 
            'pending', 
            'unpaid', 
            ?
        )
    ");

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt->execute([
        $data['total_price'],
        $data['shipping_address'],
        $data['billing_address'],
        $user_id
    ]);

    $order_id = $pdo->lastInsertId();

    // Bestellpositionen erstellen
    $stmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id, 
            product_id, 
            quantity, 
            price, 
            total_price
        ) VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($data['items'] as $item) {
        $total_price = $item['quantity'] * $item['price'];
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $total_price
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?> 