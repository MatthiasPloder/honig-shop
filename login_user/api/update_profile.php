<?php
require_once('../includes/Auth.php');

if (!Auth::isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'nicht_eingeloggt']);
    exit();
}

$host = 'localhost';
$db   = 'honig_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE users SET 
                          first_name = ?,
                          last_name = ?,
                          phone_number = ?,
                          shipping_address = ?,
                          billing_address = ?
                          WHERE user_id = ?");

    $stmt->execute([
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['phone'],
        $_POST['shipping_address'] ?? null,
        $_POST['billing_address'] ?? null,
        $_SESSION['user_id']
    ]);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Profil erfolgreich aktualisiert'
    ]);

} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Datenbankfehler: ' . $e->getMessage()
    ]);
}
?> 