<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Benutzer-ID fehlt');
    }

    $user_id = intval($_GET['id']);

    $stmt = $pdo->prepare("
        SELECT u.*, 
               COUNT(DISTINCT o.order_id) as total_orders,
               SUM(o.total_price) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.user_id = o.user_id
        WHERE u.user_id = ?
        GROUP BY u.user_id
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Benutzer nicht gefunden');
    }

    echo json_encode([
        'status' => 'success',
        'user' => $user
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 