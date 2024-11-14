-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS honig_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE honig_shop;

-- Kategorien-Tabelle
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Produkte-Tabelle
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    productname VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    weight INT,  -- in Gramm
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Benutzer-Tabelle
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    address TEXT,
    zip VARCHAR(10),
    city VARCHAR(50),
    phone VARCHAR(20),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bestellungen-Tabelle
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    zip VARCHAR(10) NOT NULL,
    city VARCHAR(50) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Bestellpositionen-Tabelle
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size INT NOT NULL,  -- in Gramm
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Zahlungen-Tabelle
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Beispiel-Kategorien einfügen
INSERT INTO categories (category_name, description) VALUES
('Blütenhonig', 'Verschiedene Sorten von Blütenhonig'),
('Waldhonig', 'Kräftiger Waldhonig aus verschiedenen Regionen'),
('Spezialitäten', 'Besondere Honigsorten und Spezialitäten'),
('Geschenksets', 'Verschiedene Honig-Geschenksets'),
('Zubehör', 'Honigzubehör und Imkereiprodukte');

-- Beispiel-Produkte einfügen
INSERT INTO products (productname, description, price, weight, stock_quantity, category_id) VALUES
('Sommerblütenhonig', 'Feiner, heller Honig aus Sommerblüten', 6.99, 500, 50, 1),
('Akazienhonig', 'Mild-süßer, heller Akazienhonig', 7.99, 500, 40, 1),
('Tannenhonig', 'Kräftiger Waldhonig aus dem Schwarzwald', 8.99, 500, 30, 2),
('Lindenhonig', 'Aromatischer Honig aus Lindenblüten', 7.49, 500, 35, 1),
('Bio-Waldhonig', 'Naturbelassener Waldhonig aus biologischer Imkerei', 9.99, 500, 25, 2),
('Honig-Geschenkset "Classic"', 'Set mit 3 verschiedenen Honigsorten à 250g', 19.99, 750, 20, 4),
('Honiglöffel aus Holz', 'Handgefertigter Honiglöffel aus Olivenholz', 4.99, NULL, 100, 5);

-- Indizes für bessere Performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_payments_order ON payments(order_id); 