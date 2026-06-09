<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('customer');
require_once dirname(__DIR__) . '/includes/layout/panel_layout.php';

$customerId = customerId();
$invoiceRepo = new InvoiceRepository($pdo);
$invoices = $invoiceRepo->findByCustomer($customerId);

panelHeader('Faktury', 'invoices');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Faktury</h1>

<table class="data-table">
    <thead>
        <tr>
            <th>Numer faktury</th>
            <th>Zamówienie</th>
            <th>Data wystawienia</th>
            <th>Termin</th>
            <th>Brutto</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($invoices as $inv): ?>
        <tr>
            <td><?= e($inv['invoice_number']) ?></td>
            <td><?= e($inv['order_number']) ?></td>
            <td><?= e(date('d.m.Y', strtotime($inv['issued_at']))) ?></td>
            <td><?= $inv['due_date'] ? e(date('d.m.Y', strtotime($inv['due_date']))) : '—' ?></td>
            <td><?= e(formatMoney((float) $inv['total_gross'])) ?></td>
            <td><span class="badge"><?= e(invoiceStatusLabel($inv['status'])) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($invoices === []): ?><p style="margin-top:16px;">Brak faktur.</p><?php endif; ?>

<?php panelFooter(); ?>
