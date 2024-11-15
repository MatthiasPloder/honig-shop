<?php
require_once('../includes/Auth.php');

// Prüfe Login-Status
if (!Auth::isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'nicht_eingeloggt'
    ]);
    exit();
}

// Wenn eingeloggt, hole die Benutzerdaten
$userData = [
    'status' => 'success',
    'user_id' => $_SESSION['user_id'],
    'email' => $_SESSION['email'],
    'notifications' => [
        'Neue Honigsorte verfügbar!',
        '10% Rabatt auf alle Blütenhonige'
    ],
    // Weitere Dashboard-Daten hier...
];

header('Content-Type: application/json');
echo json_encode($userData);
?> 