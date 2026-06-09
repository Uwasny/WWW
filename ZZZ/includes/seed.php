<?php

declare(strict_types=1);

function ensureSeedData(PDO $pdo): void
{
    $roleCount = (int) $pdo->query('SELECT COUNT(*) FROM roles')->fetchColumn();
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

    if ($roleCount > 0 && $userCount > 0) {
        return;
    }

    $pdo->beginTransaction();
    try {
        if ($roleCount === 0) {
            $pdo->exec("INSERT INTO roles (id, name, permissions) VALUES
                (1, 'admin', '{\"all\": true}'),
                (2, 'customer', '{\"orders\": true, \"invoices\": true}')");
        }

        $pdo->exec("INSERT INTO price_lists (id, name, currency) VALUES
            (1, 'Cennik standardowy', 'PLN')
            ON DUPLICATE KEY UPDATE name = VALUES(name)");

        $pdo->exec('UPDATE customers SET price_list_id = 1 WHERE id = 1 AND price_list_id IS NULL');

        $pdo->exec("INSERT INTO product_prices (product_id, price_list_id, price, min_quantity) VALUES
            (2, 1, 4999.0000, 1.000),
            (3, 1, 9.9900, 1.000)
            ON DUPLICATE KEY UPDATE price = VALUES(price)");

        $pdo->exec("INSERT INTO inventory_items (product_id, warehouse_id, quantity_on_hand, quantity_reserved, min_stock) VALUES
            (2, 1, 10.000, 0.000, 2.000),
            (3, 1, 100.000, 0.000, 10.000)
            ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand)");

        if ($userCount === 0) {
            $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
            $clientHash = password_hash('client123', PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, email, role_id, customer_id, is_active) VALUES (?, ?, ?, ?, ?, 1)');
            $stmt->execute(['admin', $adminHash, 'admin@marketflow.pl', 1, null]);
            $stmt->execute(['klient', $clientHash, 'example@example.com', 2, 1]);
        }

        $pdo->exec("INSERT INTO product_categories (id, name, parent_id) VALUES
            (1, 'Elektronika', NULL),
            (2, 'Żywność', NULL),
            (3, 'Materiały budowlane', NULL),
            (4, 'Narzędzia', NULL)
            ON DUPLICATE KEY UPDATE name = VALUES(name)");

        $pdo->exec('UPDATE products SET category_id = 1 WHERE id = 2 AND category_id IS NULL');
        $pdo->exec('UPDATE products SET category_id = 2 WHERE id = 3 AND category_id IS NULL');

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
