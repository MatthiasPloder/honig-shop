<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

// Empfange JSON-Daten
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $data) {
    // Daten aus dem JSON-Request
    $email = $data['email'];
    $password = $data['password'];
    $firstname = $data['first_name'];
    $lastname = $data['last_name'];
    $confirm_password = $data['confirm_password'];

    // Validierung
    if ($password !== $confirm_password) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Passwörter stimmen nicht überein'
        ]);
        exit();
    }

    // Password hashen
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Prüfe ob Email bereits existiert
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Diese E-Mail-Adresse ist bereits registriert'
            ]);
            exit();
        }

        // Neuen Benutzer einfügen
        $stmt = $pdo->prepare("
            INSERT INTO users (email, first_name, last_name, password_hash) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([$email, $firstname, $lastname, $hashed_password]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Registrierung erfolgreich'
        ]);

    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Datenbankfehler beim Registrieren'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ungültige Anfrage'
    ]);
}
?>
