-- Uruchom w phpMyAdmin na bazie warehouse, jeśli auto-seed PHP nie zadziała.
-- Kolumna image_path dodawana automatycznie przy starcie aplikacji (includes/migrations.php).
-- Hasła kont: admin123 (admin), client123 (klient B2B)
-- Uwaga: password_hash generuj w PHP — poniższe INSERTy dla ról i danych pomocniczych.

INSERT INTO roles (id, name, permissions) VALUES
    (1, 'admin', '{"all": true}'),
    (2, 'customer', '{"orders": true, "invoices": true}')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO price_lists (id, name, currency) VALUES
    (1, 'Cennik standardowy', 'PLN')
ON DUPLICATE KEY UPDATE name = VALUES(name);

UPDATE customers SET price_list_id = 1 WHERE id = 1;

INSERT INTO product_prices (product_id, price_list_id, price, min_quantity) VALUES
    (2, 1, 4999.0000, 1.000),
    (3, 1, 9.9900, 1.000)
ON DUPLICATE KEY UPDATE price = VALUES(price);

INSERT INTO inventory_items (product_id, warehouse_id, quantity_on_hand, quantity_reserved, min_stock) VALUES
    (2, 1, 10.000, 0.000, 2.000),
    (3, 1, 100.000, 0.000, 10.000)
ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand);

INSERT INTO product_categories (id, name, parent_id) VALUES
    (1, 'Elektronika', NULL),
    (2, 'Żywność', NULL),
    (3, 'Materiały budowlane', NULL),
    (4, 'Narzędzia', NULL)
ON DUPLICATE KEY UPDATE name = VALUES(name);

UPDATE products SET category_id = 1 WHERE id = 2;
UPDATE products SET category_id = 2 WHERE id = 3;
