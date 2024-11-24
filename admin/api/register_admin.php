<?php
session_start();
require_once('../../config/database.php');

header('Content-Type: application/json');

// Definieren Sie hier Ihren geheimen Admin-Schlüssel
define('ADMIN_KEY', 'admin_key_Imkerei_Ploder');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Überprüfen Sie die erforderlichen Felder
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['admin_key'])) {
        throw new Exception('Alle Felder müssen ausgefüllt werden');
    }
    
    // Überprüfen Sie den Admin-Schlüssel
    if ($data['admin_key'] !== ADMIN_KEY) {
        throw new Exception('Ungültiger Admin-Schlüssel');
    }
    
    // Validierung der E-Mail
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ungültige E-Mail-Adresse');
    }
    
    // Überprüfen Sie die Mindestlänge des Passworts
    if (strlen($data['password']) < 8) {
        throw new Exception('Das Passwort muss mindestens 8 Zeichen lang sein');
    }
    
    // Überprüfen Sie, ob Username oder E-Mail bereits existieren
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?");
    $stmt->execute([$data['username'], $data['email']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Benutzername oder E-Mail existiert bereits');
    }
    
    // Hash das Passwort
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Fügen Sie den neuen Admin hinzu
    $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$data['username'], $data['email'], $password_hash]);
    
    echo json_encode(['status' => 'success', 'message' => 'Admin-Account wurde erfolgreich erstellt!']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} 