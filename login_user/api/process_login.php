<?php
require_once('../includes/Auth.php');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log');

header('Content-Type: application/json');

try {
    // Lese JSON-Daten aus dem Request-Body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (!$data || !isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Bitte E-Mail und Passwort eingeben');
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = $data['password'];

    if (!$email) {
        throw new Exception('Ungültige E-Mail-Adresse');
    }

    // Datenbankverbindung
    $mysqli = new mysqli('localhost', 'root', '', 'honig_shop');
    $mysqli->set_charset('utf8mb4');

    if ($mysqli->connect_error) {
        throw new Exception('Datenbankverbindung fehlgeschlagen');
    }

    // Benutzer in der Datenbank suchen
    $stmt = $mysqli->prepare("
        SELECT user_id, email, password_hash 
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception('Datenbankabfrage fehlgeschlagen');
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception('Benutzer nicht gefunden');
    }

    // Passwort überprüfen
    if (!password_verify($password, $user['password_hash'])) {
        throw new Exception('Falsches Passwort');
    }

    // Login-Zeit aktualisieren
    $updateStmt = $mysqli->prepare("
        UPDATE users 
        SET last_login = CURRENT_TIMESTAMP,
            auth_token = ?,
            token_expires = DATE_ADD(NOW(), INTERVAL 30 DAY)
        WHERE user_id = ?
    ");

    $authToken = bin2hex(random_bytes(32));
    $updateStmt->bind_param("si", $authToken, $user['user_id']);
    $updateStmt->execute();

    // Session starten und Benutzer einloggen
    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];

    // Cookie für "Remember Me" setzen
    setcookie(
        'auth_token',
        $authToken,
        time() + (30 * 24 * 60 * 60), // 30 Tage
        '/',
        '',
        true,    // Nur über HTTPS
        true     // Nur HTTP
    );

    echo json_encode([
        'status' => 'success',
        'message' => 'Login erfolgreich',
        'user' => [
            'id' => $user['user_id'],
            'email' => $user['email']
        ]
    ]);

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Aufräumen
if (isset($stmt)) $stmt->close();
if (isset($updateStmt)) $updateStmt->close();
if (isset($mysqli)) $mysqli->close();
?> 