<?php

declare(strict_types=1);

class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findActive(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN product_categories c ON c.id = p.category_id
            WHERE p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN product_categories c ON c.id = p.category_id
            WHERE p.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $query, int $limit = 50): array
    {
        $like = '%' . $query . '%';
        $stmt = $this->pdo->prepare('SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN product_categories c ON c.id = p.category_id
            WHERE p.is_active = 1 AND (p.name LIKE ? OR p.sku LIKE ?)
            ORDER BY p.name
            LIMIT ?');
        $stmt->bindValue(1, $like);
        $stmt->bindValue(2, $like);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByCategory(?int $categoryId, ?string $type, int $limit = 50): array
    {
        $sql = 'SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN product_categories c ON c.id = p.category_id
            WHERE p.is_active = 1';
        $params = [];

        if ($categoryId) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }
        if ($type) {
            $sql .= ' AND p.type = ?';
            $params[] = $type;
        }

        $sql .= ' ORDER BY p.name LIMIT ?';
        $params[] = $limit;

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $i => $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i + 1, $param, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPrice(int $productId, int $priceListId): ?float
    {
        $stmt = $this->pdo->prepare('SELECT price FROM product_prices
            WHERE product_id = ? AND price_list_id = ?
            ORDER BY min_quantity ASC LIMIT 1');
        $stmt->execute([$productId, $priceListId]);
        $price = $stmt->fetchColumn();
        return $price !== false ? (float) $price : null;
    }

    public function getAvailableStock(int $productId): float
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(SUM(quantity_on_hand - quantity_reserved), 0)
            FROM inventory_items WHERE product_id = ?');
        $stmt->execute([$productId]);
        return (float) $stmt->fetchColumn();
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT p.*, c.name AS category_name FROM products p
            LEFT JOIN product_categories c ON c.id = p.category_id
            ORDER BY p.id DESC')->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO products (sku, name, description, image_path, category_id, unit, is_active, type, warranty_months, expiration_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['sku'],
            $data['name'],
            $data['description'] ?? '',
            $data['image_path'] ?? null,
            $data['category_id'] ?: null,
            $data['unit'] ?? 'szt',
            $data['is_active'] ?? 1,
            $data['type'] ?? 'electronic',
            $data['warranty_months'] ?: null,
            $data['expiration_date'] ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE products SET sku=?, name=?, description=?, image_path=?, category_id=?, unit=?, is_active=?, type=?, warranty_months=?, expiration_date=? WHERE id=?');
        $stmt->execute([
            $data['sku'],
            $data['name'],
            $data['description'] ?? '',
            $data['image_path'] ?? null,
            $data['category_id'] ?: null,
            $data['unit'] ?? 'szt',
            $data['is_active'] ?? 1,
            $data['type'] ?? 'electronic',
            $data['warranty_months'] ?: null,
            $data['expiration_date'] ?: null,
            $id,
        ]);
    }

    public function updateImagePath(int $id, ?string $imagePath): void
    {
        $stmt = $this->pdo->prepare('UPDATE products SET image_path = ? WHERE id = ?');
        $stmt->execute([$imagePath, $id]);
    }
}
