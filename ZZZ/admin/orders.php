<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$orderRepo = new OrderRepository($pdo);
$adminRepo = new AdminRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $orderId = (int) $_POST['order_id'];

    if (isset($_POST['status'])) {
        $orderRepo->updateStatus($orderId, $_POST['status']);
        $audit->log('orders', $orderId, 'status_changed', ['status' => $_POST['status']]);
        flash('success', 'Status zamówienia zaktualizowany.');
    }

    if (isset($_POST['shipment'])) {
        $adminRepo->upsertShipment(
            $orderId,
            trim($_POST['carrier'] ?? '') ?: null,
            trim($_POST['tracking_number'] ?? '') ?: null,
            $_POST['shipment_status'] ?? 'PENDING'
        );
        if (($_POST['shipment_status'] ?? '') === 'SHIPPED') {
            $orderRepo->updateStatus($orderId, 'SHIPPED');
        }
        $audit->log('shipments', $orderId, 'updated', ['tracking' => $_POST['tracking_number'] ?? '']);
        flash('success', 'Wysyłka zapisana.');
    }

    redirect(appUrl('/admin/orders.php?view=' . $orderId));
}

$orders = $orderRepo->all();
$viewId = (int) ($_GET['view'] ?? 0);
$viewOrder = $viewId ? $orderRepo->findById($viewId) : null;
$viewLines = $viewOrder ? $orderRepo->getLines($viewId) : [];
$viewShipment = $viewOrder ? $orderRepo->getShipment($viewId) : null;

adminHeader('Zamówienia', 'orders');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Zamówienia</h1>

<?php if ($viewOrder): ?>
<div style="background:white;padding:24px;border-radius:12px;border:1px solid var(--outline-variant);margin-bottom:24px;">
    <h2 class="font-headline-md"><?= e($viewOrder['order_number']) ?> — <?= e($viewOrder['company_name']) ?></h2>
    <p>Status: <strong><?= e(orderStatusLabel($viewOrder['status'])) ?></strong></p>
    <p>Suma brutto: <?= e(formatMoney((float) $viewOrder['total_gross'])) ?></p>

    <table class="data-table" style="margin:16px 0;">
        <thead><tr><th>Produkt</th><th>Ilość</th><th>Cena</th><th>Suma</th></tr></thead>
        <tbody>
            <?php foreach ($viewLines as $line): ?>
            <tr>
                <td><?= e($line['product_name']) ?></td>
                <td><?= e((string) $line['quantity']) ?></td>
                <td><?= e(formatMoney((float) $line['unit_price'])) ?></td>
                <td><?= e(formatMoney((float) $line['line_total'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="post" style="display:flex;gap:12px;align-items:center;margin-bottom:16px;">
        <?= csrfField() ?>
        <input type="hidden" name="order_id" value="<?= $viewId ?>">
        <select name="status">
            <?php foreach (['NEW','CONFIRMED','SHIPPED','COMPLETED','CANCELLED'] as $s): ?>
            <option value="<?= $s ?>" <?= $viewOrder['status'] === $s ? 'selected' : '' ?>><?= e(orderStatusLabel($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary">Zmień status</button>
    </form>

    <h3 class="font-headline-md" style="margin:16px 0 8px;">Wysyłka</h3>
    <form method="post" class="form-grid" style="max-width:500px;">
        <?= csrfField() ?>
        <input type="hidden" name="order_id" value="<?= $viewId ?>">
        <input type="hidden" name="shipment" value="1">
        <input type="text" name="carrier" placeholder="Przewoźnik" value="<?= e($viewShipment['carrier'] ?? '') ?>">
        <input type="text" name="tracking_number" placeholder="Numer śledzenia" value="<?= e($viewShipment['tracking_number'] ?? '') ?>">
        <select name="shipment_status">
            <?php foreach (['PENDING','SHIPPED','DELIVERED'] as $s): ?>
            <option value="<?= $s ?>" <?= ($viewShipment['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary">Zapisz wysyłkę</button>
    </form>
    <p style="margin-top:16px;"><a href="<?= e(appUrl('/admin/orders.php')) ?>">← Lista zamówień</a></p>
</div>
<?php endif; ?>

<table class="data-table">
    <thead><tr><th>Numer</th><th>Klient</th><th>Status</th><th>Brutto</th><th>Data</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e($o['order_number']) ?></td>
            <td><?= e($o['company_name']) ?></td>
            <td><?= e(orderStatusLabel($o['status'])) ?></td>
            <td><?= e(formatMoney((float) $o['total_gross'])) ?></td>
            <td><?= e(date('d.m.Y H:i', strtotime($o['created_at']))) ?></td>
            <td><a href="?view=<?= (int) $o['id'] ?>">Szczegóły</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
