<?php
class Auth {
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Prüfe Session und Cookies
        if (!isset($_SESSION['user_id'])) {
            // Versuche Auto-Login über Cookies
            if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_email'])) {
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['email'] = $_COOKIE['user_email'];
                return true;
            }
            return false;
        }
        return true;
    }

    public static function checkLoginStatus() {
        if (!self::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'nicht_eingeloggt'
            ]);
            exit();
        }
        return true;
    }
}
?> 