<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');

// Prüfe Admin-Authentifizierung
checkAdminAuth();

try {
    // Validiere Eingaben
    if (!isset($_POST['name']) || !isset($_POST['price']) || !isset($_POST['category_id'])) {
        throw new Exception('Fehlende Pflichtfelder');
    }

    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description'] ?? '');
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
    $weight = intval($_POST['weight'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');

    // Datenbankoperation
    if ($id) {
        // Update existierendes Produkt
        $sql = "UPDATE products SET 
                productname = ?, 
                price = ?, 
                category_id = ?, 
                description = ?,
                stock_quantity = ?,
                weight = ?,
                image_url = ?
                WHERE product_id = ?";
        $params = [$name, $price, $category_id, $description, $stock_quantity, $weight, $image_url, $id];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        // Neues Produkt erstellen
        $stmt = $pdo->prepare("
            INSERT INTO products (productname, price, category_id, description, stock_quantity, weight, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $price, $category_id, $description, $stock_quantity, $weight, $image_url]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => $id ? 'Produkt aktualisiert' : 'Produkt erstellt'
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 