<?php
class RateLimiter {
    private $mysqli;
    private $maxAttempts = 5;
    private $timeWindow = 900; // 1 Minuten in Sekunden

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function checkRateLimit($ip) {
        $stmt = $this->mysqli->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE ip_address = ? 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)
            AND success = 0
        ");
        $stmt->bind_param("si", $ip, $this->timeWindow);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['attempts'] >= $this->maxAttempts) {
            throw new Exception('Zu viele Login-Versuche. Bitte warten Sie 15 Minuten.');
        }
    }

    public function logAttempt($ip, $email, $success) {
        // Bei erfolgreichem Login alle vorherigen Fehlversuche löschen
        if ($success) {
            $deleteStmt = $this->mysqli->prepare("
                DELETE FROM login_attempts 
                WHERE ip_address = ? 
                AND email = ?
            ");
            $deleteStmt->bind_param("ss", $ip, $email);
            $deleteStmt->execute();
        }

        // Neuen Versuch loggen
        $stmt = $this->mysqli->prepare("
            INSERT INTO login_attempts (ip_address, email, success) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ssi", $ip, $email, $success);
        $stmt->execute();
    }

    public function resetAttempts($ip, $email) {
        $stmt = $this->mysqli->prepare("
            DELETE FROM login_attempts 
            WHERE ip_address = ? 
            AND email = ?
        ");
        $stmt->bind_param("ss", $ip, $email);
        $stmt->execute();
    }
} 