<?php
try {
    $host = 'localhost';
    $dbname = 'honig_shop';
    $username = 'root';  // Ihr Datenbankbenutzername
    $password = '';      // Ihr Datenbankpasswort

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch(PDOException $e) {
    // Fehler als JSON zurückgeben
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Datenbankverbindungsfehler'
    ]);
    exit;
}
?> 