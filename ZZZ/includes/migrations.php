<?php

declare(strict_types=1);

function ensureSchema(PDO $pdo): void
{
    $column = $pdo->query("SHOW COLUMNS FROM products LIKE 'image_path'")->fetch();
    if (!$column) {
        $pdo->exec('ALTER TABLE products ADD COLUMN image_path VARCHAR(255) DEFAULT NULL AFTER description');
    }

    $uploadDir = dirname(__DIR__) . '/public/assets/uploads/products';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
}
