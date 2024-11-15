<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectToLogin() {
    header('Location: ../login.html');
    exit();
}

// Prüfe ob der Benutzer eingeloggt ist
if (!isLoggedIn()) {
    redirectToLogin();
}
?> 