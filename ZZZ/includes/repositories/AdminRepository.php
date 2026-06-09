<?php

declare(strict_types=1);

class AdminRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function priceLists(): array
    {
        return $this->pdo->query('SELECT * FROM price_lists ORDER BY name')->fetchAll();
    }

    public function productPrices(int $priceListId): array
    {
        $stmt = $this->pdo->prepare('SELECT pp.*, p.name AS product_name, p.sku FROM product_prices pp
            JOIN products p ON p.id = pp.product_id
            WHERE pp.price_list_id = ? ORDER BY p.name');
        $stmt->execute([$priceListId]);
        return $stmt->fetchAll();
    }

    public function setProductPrice(int $productId, int $priceListId, float $price): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO product_prices (product_id, price_list_id, price, min_quantity)
            VALUES (?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE price = VALUES(price)');
        $stmt->execute([$productId, $priceListId, $price]);
    }

    public function createPriceList(string $name, string $currency = 'PLN'): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO price_lists (name, currency) VALUES (?, ?)');
        $stmt->execute([$name, $currency]);
        return (int) $this->pdo->lastInsertId();
    }

    public function upsertShipment(int $orderId, ?string $carrier, ?string $tracking, string $status): void
    {
        $existing = $this->pdo->prepare('SELECT id FROM shipments WHERE order_id = ?');
        $existing->execute([$orderId]);
        $id = $existing->fetchColumn();

        if ($id) {
            $stmt = $this->pdo->prepare('UPDATE shipments SET carrier=?, tracking_number=?, status=?, shipped_at=IF(?=\'SHIPPED\', NOW(), shipped_at) WHERE order_id=?');
            $stmt->execute([$carrier, $tracking, $status, $status, $orderId]);
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO shipments (order_id, carrier, tracking_number, status, shipped_at)
                VALUES (?, ?, ?, ?, IF(?=\'SHIPPED\', NOW(), NULL))');
            $stmt->execute([$orderId, $carrier, $tracking, $status, $status]);
        }
    }

    public function csvImports(): array
    {
        return $this->pdo->query('SELECT ci.*, c.company_name FROM csv_imports ci
            LEFT JOIN customers c ON c.id = ci.customer_id
            ORDER BY ci.created_at DESC')->fetchAll();
    }

    public function recordCsvImport(?int $customerId, string $filename, string $status): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO csv_imports (customer_id, filename, status) VALUES (?, ?, ?)');
        $stmt->execute([$customerId, $filename, $status]);
        return (int) $this->pdo->lastInsertId();
    }

    public function roles(): array
    {
        return $this->pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll();
    }
}
