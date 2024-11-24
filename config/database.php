<?php
try {
    $host = 'localhost';
    $dbname = 'honig_shop';
    $username = 'root';
    $password = '';

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    error_log("Datenbankfehler: " . $e->getMessage());
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Datenbankverbindungsfehler'
    ]);
    exit;
}
?> 