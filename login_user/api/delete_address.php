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
    
    if ($userData['shipping_address']) {
        $addresses = json_decode($userData['shipping_address'], true) ?? [];
        $addressIndex = $_POST['addressId'];
        
        // Prüfen ob Index existiert
        if (isset($addresses[$addressIndex])) {
            // Adresse aus Array entfernen
            array_splice($addresses, $addressIndex, 1);
            
            // Adressen aktualisieren
            $stmt = $pdo->prepare("UPDATE users SET shipping_address = ? WHERE user_id = ?");
            $stmt->execute([json_encode($addresses), $_SESSION['user_id']]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Adresse erfolgreich gelöscht'
            ]);
        } else {
            throw new Exception('Adresse nicht gefunden');
        }
    } else {
        throw new Exception('Keine Adressen vorhanden');
    }

} catch(Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 