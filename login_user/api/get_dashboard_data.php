<?php
require_once('../includes/Auth.php');

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'nicht_eingeloggt'
    ]);
    exit();
}

// Hole die Dashboard-Daten für den eingeloggten Benutzer
$user = Auth::getCurrentUser();

echo json_encode([
    'status' => 'success',
    'data' => [
        'user' => $user,
        // Weitere Dashboard-Daten hier...
    ]
]); 