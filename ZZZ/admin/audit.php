<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$auditRepo = new AuditRepository($pdo);
$logs = $auditRepo->recent(100);

adminHeader('Logi audytu', 'audit');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Logi audytu</h1>

<table class="data-table">
    <thead>
        <tr><th>Data</th><th>Użytkownik</th><th>Encja</th><th>ID</th><th>Akcja</th><th>Szczegóły</th></tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= e(date('d.m.Y H:i:s', strtotime($log['performed_at']))) ?></td>
            <td><?= e($log['username'] ?? 'system') ?></td>
            <td><?= e($log['entity']) ?></td>
            <td><?= e((string) ($log['entity_id'] ?? '—')) ?></td>
            <td><?= e($log['action']) ?></td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;"><?= e($log['payload'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($logs === []): ?><p style="margin-top:16px;">Brak wpisów.</p><?php endif; ?>

<?php adminFooter(); ?>
