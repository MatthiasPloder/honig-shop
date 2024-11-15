<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data (email and password)
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection parameters
    $host = 'localhost';  // Database host
    $db   = 'honig_shop'; // Database name
    $user = 'root'; // Database username
    $pass = ''; // Database password

    // Create a connection to the MySQL database
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Check the connection
    if ($mysqli->connect_error) {
        die('Connection failed: ' . $mysqli->connect_error);
    }

    // Query the database for the user based on the email
    $stmt = $mysqli->prepare("SELECT user_id, email, password_hash FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email); // Bind the email parameter to the query
    $stmt->execute();

    // Store the result
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Fetch the user data
        $stmt->bind_result($id, $db_email, $db_password);
        $stmt->fetch();

        // Verify if the password matches the hashed password stored in the database
        if (password_verify($password, $db_password)) {
            // Login successful, start session and store user data
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $db_email;
            
            // Setze Cookies für "Remember Me" (30 Tage)
            setcookie('user_id', $id, time() + (86400 * 30), '/');
            setcookie('user_email', $db_email, time() + (86400 * 30), '/');
            
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
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Ungültige Anfrage'
    ]);
}

$stmt->close();
$mysqli->close();
?>