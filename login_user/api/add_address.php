<?php
require_once('../includes/Auth.php');
require_once('../../config/database.php');

if (!Auth::isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'nicht_eingeloggt']);
    exit();
}

try {
    // Aktuelle Adressen abrufen
    $stmt = $pdo->prepare("SELECT shipping_address FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
    
    // Bestehende Adressen dekodieren oder neues Array erstellen
    $addresses = [];
    if ($userData['shipping_address']) {
        $addresses = json_decode($userData['shipping_address'], true) ?? [];
    }
    
    // Neue Adresse hinzufügen
    $newAddress = [
        'street' => $_POST['street'],
        'postalCode' => $_POST['postalCode'],
        'city' => $_POST['city'],
        'country' => $_POST['country']
    ];
    
    $addresses[] = $newAddress;

    // Adressen aktualisieren
    $stmt = $pdo->prepare("UPDATE users SET shipping_address = ? WHERE user_id = ?");
    $stmt->execute([json_encode($addresses), $_SESSION['user_id']]);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Adresse erfolgreich hinzugefügt'
    ]);

} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Datenbankfehler: ' . $e->getMessage()
    ]);
}
?> 