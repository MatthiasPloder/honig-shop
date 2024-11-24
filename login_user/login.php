<?php
session_start();
require_once('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data (email and password)
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Query the database for the user based on the email
        $stmt = $pdo->prepare("SELECT user_id, email, password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        
        // Store the result
        $user = $stmt->fetch();

        // Check if the user exists
        if ($user) {
            // Verify if the password matches the hashed password stored in the database
            if (password_verify($password, $user['password_hash'])) {
                // Login successful, start session and store user data
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                
                // Setze Cookies für "Remember Me" (30 Tage)
                setcookie('user_id', $user['user_id'], time() + (86400 * 30), '/');
                setcookie('user_email', $user['email'], time() + (86400 * 30), '/');
                
                // Sende JSON-Antwort statt Weiterleitung
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login erfolgreich'
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
                'message' => 'Kein Konto mit dieser E-Mail-Adresse gefunden'
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