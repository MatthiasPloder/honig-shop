<?php
class Auth {
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email']
        ];
    }

    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'nicht_eingeloggt'
            ]);
            exit();
        }
    }
}