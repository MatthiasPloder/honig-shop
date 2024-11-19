<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Nicht eingeloggt');
    }

    $userId = $_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $items = $data['items'] ?? [];

    // Debug-Ausgabe
    error_log('Erhaltene Items: ' . print_r($items, true));

    // Transaktion starten
    $pdo->beginTransaction();

    // Alten Warenkorb löschen
    $stmt = $pdo->prepare("DELETE FROM shopping_cart WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Neue Items einfügen
    if (!empty($items)) {
        $stmt = $pdo->prepare("
            INSERT INTO shopping_cart (user_id, product_id, quantity) 
            VALUES (?, ?, ?)
        ");

        foreach ($items as $item) {
            $stmt->execute([
                $userId,
                $item['productId'],
                $item['quantity']
            ]);
        }
    }

    $pdo->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Warenkorb erfolgreich aktualisiert'
    ]);

} catch (Exception $e) {
    error_log('Fehler in sync_cart.php: ' . $e->getMessage());
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 