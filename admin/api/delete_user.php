<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id'])) {
        throw new Exception('Benutzer-ID fehlt');
    }

    $user_id = intval($data['user_id']);

    // Beginne Transaktion
    $pdo->beginTransaction();

    // Lösche abhängige Datensätze
    $stmt = $pdo->prepare("DELETE FROM shopping_cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?)");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Lösche den Benutzer
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Benutzer nicht gefunden');
    }

    // Commit Transaktion
    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Benutzer wurde erfolgreich gelöscht'
    ]);

} catch(Exception $e) {
    // Rollback bei Fehler
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 