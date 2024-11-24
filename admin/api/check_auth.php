<?php
session_start();
require_once('../../config/database.php');

header('Content-Type: application/json');

if (isset($_SESSION['admin_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Authentifiziert'
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Nicht authentifiziert'
    ]);
}
?> 