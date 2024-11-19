<?php
require_once('../includes/Auth.php');
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    session_start();
    
    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    
    if ($isLoggedIn) {
        echo json_encode([
            'status' => 'success',
            'isLoggedIn' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['email']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'isLoggedIn' => false
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 