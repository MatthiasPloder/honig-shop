<?php
session_start();

// Lösche Session-Variablen
session_unset();
session_destroy();

// Lösche alle Login-Cookies
setcookie('user_id', '', time() - 3600, '/');
setcookie('user_email', '', time() - 3600, '/');

// Sende Erfolgsantwort
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Erfolgreich ausgeloggt']);
?> 