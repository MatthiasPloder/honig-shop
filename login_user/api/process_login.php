<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Datenbankverbindung und Überprüfung
    // ... (bisheriger Code)

    if (password_verify($password, $db_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $db_email;
        
        // Setze Cookies für "Remember Me"
        setcookie('user_id', $id, time() + (86400 * 30), '/');
        setcookie('user_email', $db_email, time() + (86400 * 30), '/');
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ungültige Anmeldedaten']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige Anfrage']);
}
?> 