<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$inventoryRepo = new InventoryRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $id = (int) $_POST['id'];
    $onHand = (float) $_POST['quantity_on_hand'];
    $minStock = (float) $_POST['min_stock'];
    $inventoryRepo->updateStock($id, $onHand, $minStock);
    $audit->log('inventory_items', $id, 'updated', ['quantity_on_hand' => $onHand, 'min_stock' => $minStock]);
    flash('success', 'Stan magazynowy zaktualizowany.');
    redirect(appUrl('/admin/inventory.php'));
}

$items = $inventoryRepo->allWithDetails();
adminHeader('Stany magazynowe', 'inventory');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Stany magazynowe</h1>

<table class="data-table">
    <thead>
        <tr><th>Produkt</th><th>Magazyn</th><th>Na stanie</th><th>Zarezerwowane</th><th>Min.</th><th>Dostępne</th><th>Edycja</th></tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= e($item['product_name']) ?> (<?= e($item['sku']) ?>)</td>
            <td><?= e($item['warehouse_name']) ?></td>
            <td><?= e((string) $item['quantity_on_hand']) ?></td>
            <td><?= e((string) $item['quantity_reserved']) ?></td>
            <td><?= e((string) $item['min_stock']) ?></td>
            <td><?= e((string) ((float)$item['quantity_on_hand'] - (float)$item['quantity_reserved'])) ?></td>
            <td>
                <form method="post" style="display:flex;gap:8px;align-items:center;">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                    <input type="number" step="0.001" name="quantity_on_hand" value="<?= e((string) $item['quantity_on_hand']) ?>" style="width:80px;padding:6px;">
                    <input type="number" step="0.001" name="min_stock" value="<?= e((string) $item['min_stock']) ?>" style="width:70px;padding:6px;">
                    <button type="submit" class="btn-secondary">Zapisz</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
