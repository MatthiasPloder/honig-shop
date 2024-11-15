<?php
require_once('../includes/Auth.php');

if (!Auth::isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'nicht_eingeloggt']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Ungültige Anfrage']);
    exit();
}

$host = 'localhost';
$db   = 'honig_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prüfe aktuelles Passwort
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($_POST['currentPassword'], $user['password_hash'])) {
        throw new Exception('Aktuelles Passwort ist falsch');
    }

    // Update Passwort
    $newPasswordHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->execute([$newPasswordHash, $_SESSION['user_id']]);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Passwort erfolgreich geändert'
    ]);

} catch(Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 