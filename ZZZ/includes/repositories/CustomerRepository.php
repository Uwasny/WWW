<?php

declare(strict_types=1);

class CustomerRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT c.*, pl.name AS price_list_name FROM customers c
            LEFT JOIN price_lists pl ON pl.id = c.price_list_id WHERE c.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT c.*, pl.name AS price_list_name FROM customers c
            LEFT JOIN price_lists pl ON pl.id = c.price_list_id ORDER BY c.company_name')->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO customers (company_name, vat_number, address, contact_email, billing_terms, price_list_id)
            VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['company_name'],
            $data['vat_number'] ?? null,
            $data['address'] ?? null,
            $data['contact_email'] ?? null,
            $data['billing_terms'] ?? 'prepayment',
            $data['price_list_id'] ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE customers SET company_name=?, vat_number=?, address=?, contact_email=?, billing_terms=?, price_list_id=? WHERE id=?');
        $stmt->execute([
            $data['company_name'],
            $data['vat_number'] ?? null,
            $data['address'] ?? null,
            $data['contact_email'] ?? null,
            $data['billing_terms'] ?? 'prepayment',
            $data['price_list_id'] ?: null,
            $id,
        ]);
    }
}
