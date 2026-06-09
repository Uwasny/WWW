<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$invoiceRepo = new InvoiceRepository($pdo);
$orderRepo = new OrderRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);

    if (!empty($_POST['create'])) {
        $orderId = (int) $_POST['order_id'];
        $order = $orderRepo->findById($orderId);
        if ($order) {
            $number = generateInvoiceNumber();
            $due = $_POST['due_date'] ?: date('Y-m-d', strtotime('+14 days'));
            $id = $invoiceRepo->create($orderId, $number, (float) $order['total_net'], (float) $order['total_gross'], $due);
            $audit->log('invoices', $id, 'created', ['invoice_number' => $number]);
            flash('success', 'Faktura wystawiona: ' . $number);
        }
    } else {
        $invoiceRepo->updateStatus((int) $_POST['invoice_id'], $_POST['status']);
        $audit->log('invoices', (int) $_POST['invoice_id'], 'status_changed', ['status' => $_POST['status']]);
        flash('success', 'Status faktury zaktualizowany.');
    }
    redirect(appUrl('/admin/invoices.php'));
}

$invoices = $invoiceRepo->all();
$orders = $orderRepo->all(50);

adminHeader('Faktury', 'invoices');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Faktury</h1>

<form method="post" class="form-grid" style="background:white;padding:24px;border-radius:12px;margin-bottom:24px;max-width:500px;">
    <?= csrfField() ?>
    <input type="hidden" name="create" value="1">
    <select name="order_id" required>
        <option value="">— Zamówienie —</option>
        <?php foreach ($orders as $o): ?>
        <option value="<?= (int) $o['id'] ?>"><?= e($o['order_number']) ?> — <?= e($o['company_name']) ?> (<?= e(formatMoney((float)$o['total_gross'])) ?>)</option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="due_date" value="<?= e(date('Y-m-d', strtotime('+14 days'))) ?>">
    <button type="submit" class="btn-primary">Wystaw fakturę</button>
</form>

<table class="data-table">
    <thead><tr><th>Numer</th><th>Zamówienie</th><th>Klient</th><th>Brutto</th><th>Status</th><th>Termin</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($invoices as $inv): ?>
        <tr>
            <td><?= e($inv['invoice_number']) ?></td>
            <td><?= e($inv['order_number']) ?></td>
            <td><?= e($inv['company_name']) ?></td>
            <td><?= e(formatMoney((float) $inv['total_gross'])) ?></td>
            <td><?= e(invoiceStatusLabel($inv['status'])) ?></td>
            <td><?= $inv['due_date'] ? e(date('d.m.Y', strtotime($inv['due_date']))) : '—' ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <?= csrfField() ?>
                    <input type="hidden" name="invoice_id" value="<?= (int) $inv['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="UNPAID" <?= $inv['status'] === 'UNPAID' ? 'selected' : '' ?>>Nieopłacona</option>
                        <option value="PAID" <?= $inv['status'] === 'PAID' ? 'selected' : '' ?>>Opłacona</option>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
