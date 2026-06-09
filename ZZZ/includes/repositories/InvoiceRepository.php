<?php

declare(strict_types=1);

class InvoiceRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByCustomer(int $customerId): array
    {
        $stmt = $this->pdo->prepare('SELECT i.*, o.order_number FROM invoices i
            JOIN orders o ON o.id = i.order_id
            WHERE o.customer_id = ?
            ORDER BY i.issued_at DESC');
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT i.*, o.order_number, c.company_name FROM invoices i
            JOIN orders o ON o.id = i.order_id
            JOIN customers c ON c.id = o.customer_id
            ORDER BY i.issued_at DESC')->fetchAll();
    }

    public function create(int $orderId, string $number, float $net, float $gross, ?string $dueDate): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO invoices (order_id, invoice_number, due_date, total_net, total_gross, status)
            VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$orderId, $number, $dueDate, $net, $gross, 'UNPAID']);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE invoices SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function countUnpaid(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'UNPAID'")->fetchColumn();
    }
}
