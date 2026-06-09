<?php

declare(strict_types=1);

class AuditRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function log(string $entity, ?int $entityId, string $action, ?array $payload, ?int $performedBy): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO audit_logs (entity, entity_id, action, payload, performed_by)
            VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $entity,
            $entityId,
            $action,
            $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null,
            $performedBy,
        ]);
    }

    public function recent(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare('SELECT a.*, u.username FROM audit_logs a
            LEFT JOIN users u ON u.id = a.performed_by
            ORDER BY a.performed_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
