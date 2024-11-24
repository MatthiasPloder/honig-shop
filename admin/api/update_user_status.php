<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['status'])) {
        throw new Exception('Benutzer-ID oder Status fehlt');
    }

    $user_id = intval($data['user_id']);
    $status = $data['status'];

    // Validiere Status
    $valid_statuses = ['active', 'inactive', 'blocked'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Ungültiger Status');
    }

    $stmt = $pdo->prepare("
        UPDATE users 
        SET status = ? 
        WHERE user_id = ?
    ");
    $stmt->execute([$status, $user_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Benutzer nicht gefunden');
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Benutzerstatus wurde aktualisiert'
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 