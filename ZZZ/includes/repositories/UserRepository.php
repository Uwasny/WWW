<?php

declare(strict_types=1);

class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT u.*, r.name AS role_name FROM users u
            JOIN roles r ON r.id = u.role_id WHERE u.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT u.*, r.name AS role_name FROM users u
            JOIN roles r ON r.id = u.role_id WHERE u.email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByLogin(string $login): ?array
    {
        $stmt = $this->pdo->prepare('SELECT u.*, r.name AS role_name FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.email = ? OR u.username = ?');
        $stmt->execute([$login, $login]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT u.*, r.name AS role_name, c.company_name FROM users u
            JOIN roles r ON r.id = u.role_id
            LEFT JOIN customers c ON c.id = u.customer_id
            ORDER BY u.id DESC')->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password_hash, email, role_id, customer_id, is_active)
            VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['email'],
            $data['role_id'],
            $data['customer_id'] ?: null,
            $data['is_active'] ?? 1,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function setCustomerId(int $userId, int $customerId): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET customer_id = ? WHERE id = ?');
        $stmt->execute([$customerId, $userId]);
    }
}
