<?php
require_once('../includes/Auth.php');

// Verhindere Caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');

try {
    $isLoggedIn = Auth::isLoggedIn();
    $user = Auth::getCurrentUser();
    
    echo json_encode([
        'status' => 'success',
        'isLoggedIn' => $isLoggedIn,
        'user' => $user
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 