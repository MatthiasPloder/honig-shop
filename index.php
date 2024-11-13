<?php
// Verbinde mit der SQLite-Datenbank
$dsn = 'sqlite:honig-shop.db'; // Hier den Pfad zur SQLite-Datei anpassen
try {
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Konnte keine Verbindung zur Datenbank herstellen: " . $e->getMessage());
}

// API-Endpunkt: Alle Produkte abrufen
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'get_products') {
    $stmt = $db->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
    exit;
}

// API-Endpunkt: Bestellungen hinzufügen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'create_order') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['product_id']) && isset($data['quantity'])) {
        $stmt = $db->prepare("INSERT INTO orders (product_id, quantity) VALUES (:product_id, :quantity)");
        $stmt->bindParam(':product_id', $data['product_id']);
        $stmt->bindParam(':quantity', $data['quantity']);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Bestellung erfolgreich']);
        } else {
            echo json_encode(['message' => 'Fehler beim Erstellen der Bestellung']);
        }
    } else {
        echo json_encode(['message' => 'Fehlende Daten']);
    }
    exit;
}
?>