<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');
checkAdminAuth();

try {
    if (!isset($_POST['name'])) {
        throw new Exception('Kategoriename fehlt');
    }

    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$name, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->execute([$name]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => $id ? 'Kategorie aktualisiert' : 'Kategorie erstellt'
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 