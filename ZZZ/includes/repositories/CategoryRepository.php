<?php

declare(strict_types=1);

class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM product_categories ORDER BY name')->fetchAll();
    }

    public function allWithProductCounts(): array
    {
        return $this->pdo->query('SELECT c.*, COUNT(p.id) AS product_count
            FROM product_categories c
            LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
            GROUP BY c.id
            ORDER BY c.name')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM product_categories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM product_categories WHERE name = ?');
        $stmt->execute([$name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $name, ?int $parentId = null): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO product_categories (name, parent_id) VALUES (?, ?)');
        $stmt->execute([$name, $parentId]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, ?int $parentId): void
    {
        $stmt = $this->pdo->prepare('UPDATE product_categories SET name = ?, parent_id = ? WHERE id = ?');
        $stmt->execute([$name, $parentId, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM product_categories WHERE id = ?');
        $stmt->execute([$id]);
    }
}
