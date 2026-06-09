<?php

declare(strict_types=1);

class OrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByCustomer(int $customerId, int $limit = 50): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $customerId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT o.*, c.company_name FROM orders o
            JOIN customers c ON c.id = o.customer_id WHERE o.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByIdForCustomer(int $id, int $customerId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE id = ? AND customer_id = ?');
        $stmt->execute([$id, $customerId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getLines(int $orderId): array
    {
        $stmt = $this->pdo->prepare('SELECT ol.*, p.name AS product_name, p.sku, w.name AS warehouse_name
            FROM order_lines ol
            JOIN products p ON p.id = ol.product_id
            LEFT JOIN warehouses w ON w.id = ol.warehouse_id
            WHERE ol.order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function getShipment(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM shipments WHERE order_id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare('SELECT o.*, c.company_name FROM orders o
            JOIN customers c ON c.id = o.customer_id
            ORDER BY o.created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM orders WHERE status = ?');
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function earningsTodayTotal(): float
    {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_gross), 0) FROM orders
            WHERE DATE(created_at) = CURDATE() AND status != 'CANCELLED'");
        return (float) $stmt->fetchColumn();
    }

    public function earningsTodayNet(): float
    {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_net), 0) FROM orders
            WHERE DATE(created_at) = CURDATE() AND status != 'CANCELLED'");
        return (float) $stmt->fetchColumn();
    }

    public function countToday(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM orders
            WHERE DATE(created_at) = CURDATE() AND status != 'CANCELLED'")->fetchColumn();
    }

    /** @return array<int, float> Godzina (0–23) => przychód brutto */
    public function earningsTodayByHour(): array
    {
        $hours = array_fill(0, 24, 0.0);

        $stmt = $this->pdo->query("SELECT HOUR(created_at) AS hour_slot, COALESCE(SUM(total_gross), 0) AS total
            FROM orders
            WHERE DATE(created_at) = CURDATE() AND status != 'CANCELLED'
            GROUP BY HOUR(created_at)");

        foreach ($stmt->fetchAll() as $row) {
            $hours[(int) $row['hour_slot']] = (float) $row['total'];
        }

        return $hours;
    }
}
