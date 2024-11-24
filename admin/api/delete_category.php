<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['category_id'])) {
        throw new Exception('Kategorie ID fehlt');
    }
    
    $category_id = intval($data['category_id']);

    // Prüfen, ob Produkte in dieser Kategorie existieren
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        throw new Exception('Diese Kategorie kann nicht gelöscht werden, da ihr noch Produkte zugeordnet sind');
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Kategorie wurde erfolgreich gelöscht'
        ]);
    } else {
        throw new Exception('Kategorie konnte nicht gefunden werden');
    }

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 