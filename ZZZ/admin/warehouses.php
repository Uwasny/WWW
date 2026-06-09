<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$inventoryRepo = new InventoryRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $id = $inventoryRepo->createWarehouse([
        'code' => trim($_POST['code'] ?? ''),
        'name' => trim($_POST['name'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ]);
    $audit->log('warehouses', $id, 'created', ['code' => $_POST['code']]);
    flash('success', 'Magazyn dodany.');
    redirect(appUrl('/admin/warehouses.php'));
}

$warehouses = $inventoryRepo->warehouses();
adminHeader('Magazyny', 'warehouses');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Magazyny</h1>

<form method="post" class="form-grid" style="background:white;padding:24px;border-radius:12px;border:1px solid var(--outline-variant);margin-bottom:24px;">
    <?= csrfField() ?>
    <input type="text" name="code" placeholder="Kod" required>
    <input type="text" name="name" placeholder="Nazwa" required>
    <input type="text" name="address" placeholder="Adres">
    <button type="submit" class="btn-primary">Dodaj magazyn</button>
</form>

<table class="data-table">
    <thead><tr><th>ID</th><th>Kod</th><th>Nazwa</th><th>Adres</th><th>Utworzono</th></tr></thead>
    <tbody>
        <?php foreach ($warehouses as $w): ?>
        <tr>
            <td><?= (int) $w['id'] ?></td>
            <td><?= e($w['code']) ?></td>
            <td><?= e($w['name']) ?></td>
            <td><?= e($w['address'] ?? '—') ?></td>
            <td><?= e(date('d.m.Y', strtotime($w['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
