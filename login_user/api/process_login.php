<?php
require_once('../includes/Auth.php');
require_once('../includes/RateLimiter.php');

// Strikte Konfiguration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

header('Content-Type: application/json');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

try {
    $mysqli = new mysqli('localhost', 'root', '', 'honig_shop');
    $mysqli->set_charset('utf8mb4');

    // Rate Limiting prüfen
    $rateLimiter = new RateLimiter($mysqli);
    $rateLimiter->checkRateLimit($_SERVER['REMOTE_ADDR']);

    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (!$data || !isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Ungültige Anfrage');
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = $data['password'];

    if (!$email) {
        throw new Exception('Ungültige E-Mail-Adresse');
    }

    // Zeitkonstanter Vergleich für die E-Mail-Suche
    $stmt = $mysqli->prepare("
        SELECT user_id, email, password_hash, account_status, failed_attempts
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Konstante Zeit simulieren, auch wenn Benutzer nicht existiert
    if (!$user) {
        password_verify($password, '$2y$10$abcdefghijklmnopqrstuvwxyz012345');
        $rateLimiter->logAttempt($_SERVER['REMOTE_ADDR'], $email, 0);
        throw new Exception('Ungültige Anmeldedaten');
    }

    // Prüfen ob Account gesperrt ist
    if ($user['account_status'] === 'locked') {
        throw new Exception('Ihr Account wurde gesperrt. Bitte kontaktieren Sie den Support.');
    }

    // Passwort überprüfen
    if (!password_verify($password, $user['password_hash'])) {
        // Fehlgeschlagene Versuche erhöhen
        $updateStmt = $mysqli->prepare("
            UPDATE users 
            SET failed_attempts = failed_attempts + 1,
                account_status = CASE 
                    WHEN failed_attempts + 1 >= 10 THEN 'locked'
                    ELSE account_status 
                END
            WHERE user_id = ?
        ");
        $updateStmt->bind_param("i", $user['user_id']);
        $updateStmt->execute();

        $rateLimiter->logAttempt($_SERVER['REMOTE_ADDR'], $email, 0);
        throw new Exception('Ungültige Anmeldedaten');
    }

    // Erfolgreicher Login - Reset der Fehlversuche
    $updateStmt = $mysqli->prepare("
        UPDATE users 
        SET failed_attempts = 0,
            last_login = CURRENT_TIMESTAMP,
            auth_token = ?,
            token_expires = DATE_ADD(NOW(), INTERVAL 30 DAY)
        WHERE user_id = ?
    ");

    $authToken = bin2hex(random_bytes(32));
    $updateStmt->bind_param("si", $authToken, $user['user_id']);
    $updateStmt->execute();

    // Session mit erhöhter Sicherheit starten
    session_start();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['last_activity'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    $rateLimiter->logAttempt($_SERVER['REMOTE_ADDR'], $email, 1);

    // Sicheres Cookie setzen
    setcookie(
        'auth_token',
        $authToken,
        [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]
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