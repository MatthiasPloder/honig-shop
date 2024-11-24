<?php
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($pdo)) {
        throw new Exception('Keine Datenbankverbindung');
    }

    $stmt = $pdo->query("SELECT category_id as id, category_name as name FROM categories ORDER BY category_name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($categories === false) {
        throw new Exception('Fehler beim Abrufen der Kategorien');
    }

    echo json_encode([
        'status' => 'success',
        'categories' => $categories
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler beim Laden der Kategorien: ' . $e->getMessage()
    ]);
} 