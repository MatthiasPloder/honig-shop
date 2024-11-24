<?php
session_start();
require_once('../../config/database.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');

// Prüfe Admin-Authentifizierung
checkAdminAuth();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['product_id'])) {
        throw new Exception('Produkt ID fehlt');
    }
    
    $product_id = intval($data['product_id']);
    
    // Hole zuerst das aktuelle Bild
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    // Prüfen Sie, ob das Produkt in order_items oder shopping_cart verwendet wird
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM order_items WHERE product_id = ?) as order_count,
            (SELECT COUNT(*) FROM shopping_cart WHERE product_id = ?) as cart_count
    ");
    $stmt->execute([$product_id, $product_id]);
    $usage = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usage['order_count'] > 0 || $usage['cart_count'] > 0) {
        // Wenn das Produkt verwendet wird, deaktivieren Sie es stattdessen
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = 0 WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Produkt wurde deaktiviert, da es in Bestellungen oder Warenkörben verwendet wird'
        ]);
    } else {
        // Wenn das Produkt nicht verwendet wird, können wir es löschen
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Produkt wurde erfolgreich gelöscht'
            ]);
        } else {
            throw new Exception('Produkt konnte nicht gefunden werden');
        }
    }

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler beim Löschen des Produkts: ' . $e->getMessage()
    ]);
}
?> 