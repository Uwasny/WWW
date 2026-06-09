<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('customer');
require_once dirname(__DIR__) . '/includes/layout/panel_layout.php';

$customerId = customerId();
$orderId = (int) ($_GET['id'] ?? 0);
$orderRepo = new OrderRepository($pdo);
$order = $orderRepo->findByIdForCustomer($orderId, $customerId);

if (!$order) {
    flash('error', 'Zamówienie nie zostało znalezione.');
    redirect(appUrl('/panel/orders.php'));
}

$lines = $orderRepo->getLines($orderId);
$shipment = $orderRepo->getShipment($orderId);

panelHeader('Zamówienie ' . $order['order_number'], 'orders');
?>

<h1 class="font-headline-lg" style="margin-bottom:8px;"><?= e($order['order_number']) ?></h1>
<p style="margin-bottom:24px;color:var(--on-surface-variant);">Status: <span class="badge"><?= e(orderStatusLabel($order['status'])) ?></span></p>

<h2 class="font-headline-md" style="margin-bottom:16px;">Pozycje zamówienia</h2>
<table class="data-table" style="margin-bottom:32px;">
    <thead>
        <tr><th>Produkt</th><th>SKU</th><th>Magazyn</th><th>Ilość</th><th>Cena</th><th>Suma</th></tr>
    </thead>
    <tbody>
        <?php foreach ($lines as $line): ?>
        <tr>
            <td><?= e($line['product_name']) ?></td>
            <td><?= e($line['sku']) ?></td>
            <td><?= e($line['warehouse_name'] ?? '—') ?></td>
            <td><?= e((string) $line['quantity']) ?></td>
            <td><?= e(formatMoney((float) $line['unit_price'])) ?></td>
            <td><?= e(formatMoney((float) $line['line_total'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="text-align:right;margin-bottom:32px;">
    <p>Netto: <?= e(formatMoney((float) $order['total_net'])) ?></p>
    <p style="font-size:20px;font-weight:700;color:var(--primary);">Brutto: <?= e(formatMoney((float) $order['total_gross'])) ?></p>
</div>

<?php if ($shipment): ?>
<h2 class="font-headline-md" style="margin-bottom:16px;">Wysyłka</h2>
<div class="order-card-panel">
    <p>Przewoźnik: <?= e($shipment['carrier'] ?? '—') ?></p>
    <p>Numer śledzenia: <?= e($shipment['tracking_number'] ?? '—') ?></p>
    <p>Status: <?= e($shipment['status']) ?></p>
    <?php if ($shipment['shipped_at']): ?>
    <p>Data wysyłki: <?= e(date('d.m.Y H:i', strtotime($shipment['shipped_at']))) ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>

<a href="<?= e(appUrl('/panel/orders.php')) ?>">← Powrót do listy</a>

<?php panelFooter(); ?>
