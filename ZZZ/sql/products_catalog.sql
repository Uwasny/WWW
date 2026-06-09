-- Kategorie i produkty katalogowe (bez zdjęć)
SET NAMES utf8mb4;

INSERT INTO product_categories (id, name, parent_id) VALUES
    (1, 'Elektronika', NULL),
    (2, 'Żywność', NULL),
    (3, 'Materiały budowlane', NULL),
    (4, 'Narzędzia', NULL)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO products (sku, name, description, category_id, unit, is_active, type, warranty_months, expiration_date) VALUES
    ('FOOD-0002', 'Mleko UHT 3,2% 1L', 'Mleko pasteryzowane UHT, karton 1 litr', 2, 'szt', 1, 'food', NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
    ('FOOD-0003', 'Chleb pszenny 500 g', 'Świeży chleb pszenny, opakowanie 500 g', 2, 'szt', 1, 'food', NULL, DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
    ('FOOD-0004', 'Masło extra 200 g', 'Masło 82% tłuszczu, kostka 200 g', 2, 'szt', 1, 'food', NULL, DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
    ('FOOD-0005', 'Jogurt naturalny 400 g', 'Jogurt naturalny, kubek 400 g', 2, 'szt', 1, 'food', NULL, DATE_ADD(CURDATE(), INTERVAL 14 DAY)),
    ('BUILD-0001', 'Cement portlandzki 25 kg', 'Cement CEM I 42,5 R, worek 25 kg', 3, 'szt', 1, 'building', NULL, NULL),
    ('BUILD-0002', 'Pustak ceramiczny 12×25×38', 'Pustak ceramiczny pełny, format 12×25×38 cm', 3, 'szt', 1, 'building', NULL, NULL),
    ('BUILD-0003', 'Wełna mineralna 100 mm', 'Mata z wełny mineralnej, grubość 100 mm, 6 m²', 3, 'opak', 1, 'building', NULL, NULL),
    ('BUILD-0004', 'Grunt głęboko penetrujący 5 L', 'Grunt pod tynki i farby, kanister 5 litrów', 3, 'szt', 1, 'building', NULL, NULL),
    ('TOOL-0001', 'Wiertarka udarowa 800 W', 'Wiertarka udarowa z regulacją obrotów, 800 W', 4, 'szt', 1, 'tools', 24, NULL),
    ('TOOL-0002', 'Zestaw kluczy nasadowych 1/2"', 'Zestaw 32 elementów, grzechotka 1/2 cala', 4, 'kpl', 1, 'tools', 12, NULL),
    ('TOOL-0003', 'Poziomica aluminiowa 60 cm', 'Poziomica 3 libelle, profil aluminiowy 60 cm', 4, 'szt', 1, 'tools', 24, NULL),
    ('TOOL-0004', 'Młotek ciesielski 500 g', 'Młotek ciesielski z trzonkiem fibrowym, 500 g', 4, 'szt', 1, 'tools', 12, NULL),
    ('ELECT-0002', 'Monitor 27" QHD', 'Monitor IPS 27 cali, rozdzielczość 2560×1440', 1, 'szt', 1, 'electronic', 36, NULL),
    ('ELECT-0003', 'Słuchawki bezprzewodowe BT', 'Słuchawki nauszne Bluetooth 5.3 z ANC', 1, 'szt', 1, 'electronic', 24, NULL),
    ('ELECT-0004', 'Router Wi-Fi 6', 'Router dual-band AX3000, 4 porty Gigabit', 1, 'szt', 1, 'electronic', 24, NULL),
    ('ELECT-0005', 'Klawiatura mechaniczna', 'Klawiatura mechaniczna RGB, przełączniki czerwone', 1, 'szt', 1, 'electronic', 24, NULL)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    category_id = VALUES(category_id),
    unit = VALUES(unit),
    is_active = VALUES(is_active),
    type = VALUES(type),
    warranty_months = VALUES(warranty_months),
    expiration_date = VALUES(expiration_date);

INSERT INTO product_prices (product_id, price_list_id, price, min_quantity)
SELECT p.id, 1, v.price, 1.000
FROM products p
JOIN (
    SELECT 'FOOD-0002' AS sku, 4.4900 AS price UNION ALL
    SELECT 'FOOD-0003', 5.9900 UNION ALL
    SELECT 'FOOD-0004', 8.4900 UNION ALL
    SELECT 'FOOD-0005', 3.7900 UNION ALL
    SELECT 'BUILD-0001', 29.9000 UNION ALL
    SELECT 'BUILD-0002', 3.4500 UNION ALL
    SELECT 'BUILD-0003', 89.0000 UNION ALL
    SELECT 'BUILD-0004', 42.5000 UNION ALL
    SELECT 'TOOL-0001', 249.0000 UNION ALL
    SELECT 'TOOL-0002', 189.0000 UNION ALL
    SELECT 'TOOL-0003', 54.9000 UNION ALL
    SELECT 'TOOL-0004', 39.9000 UNION ALL
    SELECT 'ELECT-0002', 1299.0000 UNION ALL
    SELECT 'ELECT-0003', 349.0000 UNION ALL
    SELECT 'ELECT-0004', 399.0000 UNION ALL
    SELECT 'ELECT-0005', 279.0000
) v ON v.sku = p.sku
ON DUPLICATE KEY UPDATE price = VALUES(price);

INSERT INTO inventory_items (product_id, warehouse_id, quantity_on_hand, quantity_reserved, min_stock)
SELECT p.id, 1, v.qty, 0.000, v.min_stock
FROM products p
JOIN (
    SELECT 'FOOD-0002' AS sku, 240.000 AS qty, 40.000 AS min_stock UNION ALL
    SELECT 'FOOD-0003', 80.000, 15.000 UNION ALL
    SELECT 'FOOD-0004', 120.000, 20.000 UNION ALL
    SELECT 'FOOD-0005', 160.000, 25.000 UNION ALL
    SELECT 'BUILD-0001', 500.000, 50.000 UNION ALL
    SELECT 'BUILD-0002', 2000.000, 200.000 UNION ALL
    SELECT 'BUILD-0003', 60.000, 8.000 UNION ALL
    SELECT 'BUILD-0004', 90.000, 12.000 UNION ALL
    SELECT 'TOOL-0001', 35.000, 5.000 UNION ALL
    SELECT 'TOOL-0002', 48.000, 6.000 UNION ALL
    SELECT 'TOOL-0003', 72.000, 10.000 UNION ALL
    SELECT 'TOOL-0004', 110.000, 15.000 UNION ALL
    SELECT 'ELECT-0002', 18.000, 3.000 UNION ALL
    SELECT 'ELECT-0003', 45.000, 8.000 UNION ALL
    SELECT 'ELECT-0004', 32.000, 5.000 UNION ALL
    SELECT 'ELECT-0005', 55.000, 8.000
) v ON v.sku = p.sku
ON DUPLICATE KEY UPDATE
    quantity_on_hand = VALUES(quantity_on_hand),
    min_stock = VALUES(min_stock);
