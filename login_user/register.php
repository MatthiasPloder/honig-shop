<?php
session_start();

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

    // Datenbankverbindung
    $host = 'localhost';
    $db   = 'honig_shop';
    $user = 'root';
    $pass = '';

    try {
        $mysqli = new mysqli($host, $user, $pass, $db);

        if ($mysqli->connect_error) {
            throw new Exception('Datenbankverbindung fehlgeschlagen: ' . $mysqli->connect_error);
        }

        // Prüfe ob Email bereits existiert
        $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Diese E-Mail-Adresse ist bereits registriert'
            ]);
            exit();
        }

        // Neuen Benutzer einfügen
        $stmt = $mysqli->prepare("INSERT INTO users (email, first_name, last_name, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $firstname, $lastname, $hashed_password);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Registrierung erfolgreich'
            ]);
        } else {
            throw new Exception('Fehler beim Speichern der Daten');
        }

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($mysqli)) $mysqli->close();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ungültige Anfrage'
    ]);
}
?>
