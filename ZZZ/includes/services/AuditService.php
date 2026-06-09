<?php

declare(strict_types=1);

class AuditService
{
    public function __construct(private AuditRepository $audit)
    {
    }

    public function log(string $entity, ?int $entityId, string $action, ?array $payload = null): void
    {
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $this->audit->log($entity, $entityId, $action, $payload, $userId);
    }
}
