<?php

declare(strict_types=1);

class InventoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allWithDetails(): array
    {
        return $this->pdo->query('SELECT i.*, p.name AS product_name, p.sku, w.name AS warehouse_name, w.code AS warehouse_code
            FROM inventory_items i
            JOIN products p ON p.id = i.product_id
            JOIN warehouses w ON w.id = i.warehouse_id
            ORDER BY p.name')->fetchAll();
    }

    public function findWarehouseWithStock(int $productId, float $quantity): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inventory_items
            WHERE product_id = ? AND (quantity_on_hand - quantity_reserved) >= ?
            ORDER BY (quantity_on_hand - quantity_reserved) DESC LIMIT 1');
        $stmt->execute([$productId, $quantity]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function reserve(int $inventoryId, float $quantity): void
    {
        $stmt = $this->pdo->prepare('UPDATE inventory_items SET quantity_reserved = quantity_reserved + ? WHERE id = ?');
        $stmt->execute([$quantity, $inventoryId]);
    }

    public function updateStock(int $id, float $onHand, float $minStock): void
    {
        $stmt = $this->pdo->prepare('UPDATE inventory_items SET quantity_on_hand = ?, min_stock = ? WHERE id = ?');
        $stmt->execute([$onHand, $minStock, $id]);
    }

    public function lowStock(): array
    {
        return $this->pdo->query('SELECT i.*, p.name AS product_name, w.name AS warehouse_name
            FROM inventory_items i
            JOIN products p ON p.id = i.product_id
            JOIN warehouses w ON w.id = i.warehouse_id
            WHERE i.quantity_on_hand <= i.min_stock')->fetchAll();
    }

    public function warehouses(): array
    {
        return $this->pdo->query('SELECT * FROM warehouses ORDER BY name')->fetchAll();
    }

    public function createWarehouse(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO warehouses (code, name, address) VALUES (?, ?, ?)');
        $stmt->execute([$data['code'], $data['name'], $data['address'] ?? null]);
        return (int) $this->pdo->lastInsertId();
    }
}
