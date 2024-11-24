<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    $stmt = $pdo->query("
        SELECT 
            user_id,
            first_name,
            last_name,
            email,
            phone_number,
            shipping_address,
            billing_address,
            DATE_FORMAT(date_created, '%Y-%m-%d %H:%i:%s') as date_created,
            DATE_FORMAT(last_login, '%Y-%m-%d %H:%i:%s') as last_login,
            failed_attempts,
            account_status,
            DATE_FORMAT(last_password_change, '%Y-%m-%d %H:%i:%s') as last_password_change
        FROM users 
        ORDER BY date_created DESC
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'users' => $users
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler beim Laden der Benutzer: ' . $e->getMessage()
    ]);
}
?> 