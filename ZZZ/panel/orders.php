<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('customer');
require_once dirname(__DIR__) . '/includes/layout/panel_layout.php';

$customerId = customerId();
$orderRepo = new OrderRepository($pdo);
$orders = $orderRepo->findByCustomer($customerId);

panelHeader('Moje zamówienia', 'orders');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Moje zamówienia</h1>

<table class="data-table">
    <thead>
        <tr>
            <th>Numer</th>
            <th>Data</th>
            <th>Status</th>
            <th>Netto</th>
            <th>Brutto</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= e($order['order_number']) ?></td>
            <td><?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></td>
            <td><span class="badge"><?= e(orderStatusLabel($order['status'])) ?></span></td>
            <td><?= e(formatMoney((float) $order['total_net'])) ?></td>
            <td><?= e(formatMoney((float) $order['total_gross'])) ?></td>
            <td><a href="<?= e(appUrl('/panel/order.php?id=' . $order['id'])) ?>">Szczegóły</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($orders === []): ?><p style="margin-top:16px;">Brak zamówień.</p><?php endif; ?>

<?php panelFooter(); ?>
