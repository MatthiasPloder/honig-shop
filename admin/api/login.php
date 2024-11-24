<?php
session_start();
require_once('../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];

    try {
        // Query für Admin-Login
        $stmt = $pdo->prepare("SELECT admin_id, username, password_hash FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        
        $admin = $stmt->fetch();

        if ($admin) {
            if (password_verify($password, $admin['password_hash'])) {
                // Admin-Login erfolgreich
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['is_admin'] = true; // Wichtig für Admin-Rechte
                
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Admin-Login erfolgreich'
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Ungültiges Passwort'
                ]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Admin-Account nicht gefunden'
            ]);
        }
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Datenbankfehler: ' . $e->getMessage()
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Ungültige Anfrage'
    ]);
}
?> 