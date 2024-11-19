<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->query("
        SELECT 
            product_id,
            productname,
            description,
            price,
            weight,
            stock_quantity,
            image_url,
            category_id
        FROM products
        WHERE stock_quantity > 0
        ORDER BY productname ASC
    ");
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$products) {
        echo json_encode([
            'status' => 'success',
            'products' => []
        ]);
        exit;
    }
    
    // Formatiere die Preise und andere Werte
    foreach ($products as &$product) {
        $product['price'] = number_format((float)$product['price'], 2, '.', '');
        $product['weight'] = (int)$product['weight'];
        $product['stock_quantity'] = (int)$product['stock_quantity'];
    }
    
    echo json_encode([
        'status' => 'success',
        'products' => $products
    ]);

} catch(PDOException $e) {
    error_log("Datenbankfehler: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Datenbankfehler beim Laden der Produkte'
    ]);
}
?>