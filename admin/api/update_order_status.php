<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['order_id']) || !isset($data['order_status']) || !isset($data['payment_status'])) {
        throw new Exception('Erforderliche Daten fehlen');
    }

    $order_id = intval($data['order_id']);
    $order_status = $data['order_status'];
    $payment_status = $data['payment_status'];

    // Validiere Status
    $valid_order_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
    $valid_payment_statuses = ['unpaid', 'paid'];

    if (!in_array($order_status, $valid_order_statuses)) {
        throw new Exception('Ungültiger Bestellstatus');
    }

    if (!in_array($payment_status, $valid_payment_statuses)) {
        throw new Exception('Ungültiger Zahlungsstatus');
    }

    $stmt = $pdo->prepare("
        UPDATE orders 
        SET order_status = ?, payment_status = ?
        WHERE order_id = ?
    ");
    $stmt->execute([$order_status, $payment_status, $order_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Bestellung nicht gefunden');
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Bestellstatus wurde aktualisiert'
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 