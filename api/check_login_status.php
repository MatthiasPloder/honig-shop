<?php
require_once('../login_user/includes/Auth.php');

header('Content-Type: application/json');
echo json_encode([
    'isLoggedIn' => Auth::isLoggedIn(),
    'userEmail' => isset($_SESSION['email']) ? $_SESSION['email'] : null
]);
?> 