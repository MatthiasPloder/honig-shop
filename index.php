<?php
session_start();
require_once('config/database.php');

// Alle Produkte abrufen
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'get_products') {
    try {
        $stmt = $pdo->query("SELECT p.*, c.category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.category_id 
                           WHERE p.stock_quantity > 0");
        $products = $stmt->fetchAll();
        echo json_encode($products);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
    exit;
}

// Bestellung erstellen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'create_order') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $pdo->beginTransaction();
        
        // Bestellung in orders-Tabelle einfügen
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount) 
                             VALUES (?, NOW(), ?)");
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $stmt->execute([$userId, $data['total']]);
        
        $orderId = $pdo->lastInsertId();
        
        // Bestellpositionen in order_items-Tabelle einfügen
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                             VALUES (?, ?, ?, ?)");
        
        foreach ($data['items'] as $item) {
            // Prüfe Verfügbarkeit
            $checkStock = $pdo->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
            $checkStock->execute([$item['productId']]);
            $currentStock = $checkStock->fetchColumn();
            
            if ($currentStock < $item['quantity']) {
                throw new Exception("Produkt nicht mehr in ausreichender Menge verfügbar");
            }
            
            // Füge Bestellposition ein
            $stmt->execute([
                $orderId,
                $item['productId'],
                $item['quantity'],
                $item['price']
            ]);
            
            // Aktualisiere Bestand
            $updateStock = $pdo->prepare("UPDATE products 
                                       SET stock_quantity = stock_quantity - ? 
                                       WHERE product_id = ?");
            $updateStock->execute([$item['quantity'], $item['productId']]);
        }
        
        // Zahlung in payments-Tabelle einfügen
        $stmt = $pdo->prepare("INSERT INTO payments (order_id, payment_date, amount) 
                             VALUES (?, NOW(), ?)");
        $stmt->execute([$orderId, $data['total']]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'order_id' => $orderId]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Warenkorb-Status prüfen
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'check_stock') {
    try {
        $productId = $_GET['product_id'];
        $quantity = $_GET['quantity'];
        
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        echo json_encode([
            'available' => $product && $product['stock_quantity'] >= $quantity
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
    exit;
}
?>