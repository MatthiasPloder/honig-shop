<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

try {
    // Prüfen ob Benutzer eingeloggt ist
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Bitte melden Sie sich an, um Produkte in den Warenkorb zu legen');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    $productId = $data['product_id'];
    $quantity = $data['quantity'];

    // Prüfen ob Produkt bereits im Warenkorb ist
    $stmt = $pdo->prepare("
        SELECT cart_id, quantity 
        FROM shopping_cart 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch();

    if ($existingItem) {
        // Wenn ja, Menge erhöhen
        $stmt = $pdo->prepare("
            UPDATE shopping_cart 
            SET quantity = quantity + ? 
            WHERE cart_id = ?
        ");
        $stmt->execute([$quantity, $existingItem['cart_id']]);
    } else {
        // Wenn nein, neu hinzufügen
        $stmt = $pdo->prepare("
            INSERT INTO shopping_cart (user_id, product_id, quantity) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $productId, $quantity]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Produkt wurde zum Warenkorb hinzugefügt'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 