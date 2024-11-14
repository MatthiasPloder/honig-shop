<?php
$servername = "localhost";
$username = "root"; // Deinen DB-Benutzernamen hier eingeben
$password = ""; // Dein DB-Passwort hier eingeben
$dbname = "honig_shop"; // Deinen DB-Namen hier eingeben

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Überprüfen, ob die erforderlichen POST-Daten gesendet wurden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productname = $_POST['productname'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $stock_quantity = $_POST['stock_quantity'];
    $image_url = $_POST['image_url'];
    $category_id = $_POST['category_id'];

    // SQL-INSERT-Anweisung
    $sql = "INSERT INTO products (productname, description, price, weight, stock_quantity, image_url, category_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiisi", $productname, $description, $price, $weight, $stock_quantity, $image_url, $category_id);

    if ($stmt->execute()) {
        echo "Produkt erfolgreich hinzugefügt!";
    } else {
        echo "Fehler: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>