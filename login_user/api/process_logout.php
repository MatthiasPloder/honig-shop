<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Session-Variablen löschen
    $_SESSION = array();

    // Session-Cookie löschen
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }

    // Session zerstören
    session_destroy();

    // Erfolg als JSON zurückgeben
    echo json_encode([
        'status' => 'success',
        'message' => 'Erfolgreich ausgeloggt'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler beim Ausloggen: ' . $e->getMessage()
    ]);
}
?> 