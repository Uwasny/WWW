<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$customerRepo = new CustomerRepository($pdo);
$adminRepo = new AdminRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $data = [
        'company_name' => trim($_POST['company_name'] ?? ''),
        'vat_number' => trim($_POST['vat_number'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'billing_terms' => $_POST['billing_terms'] ?? 'prepayment',
        'price_list_id' => (int) ($_POST['price_list_id'] ?? 0) ?: null,
    ];

    if (!empty($_POST['id'])) {
        $id = (int) $_POST['id'];
        $customerRepo->update($id, $data);
        $audit->log('customers', $id, 'updated', $data);
    } else {
        $id = $customerRepo->create($data);
        $audit->log('customers', $id, 'created', $data);
    }
    flash('success', 'Klient zapisany.');
    redirect(appUrl('/admin/customers.php'));
}

$customers = $customerRepo->all();
$priceLists = $adminRepo->priceLists();
$edit = !empty($_GET['edit']) ? $customerRepo->findById((int) $_GET['edit']) : null;

adminHeader('Klienci', 'customers');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Klienci B2B</h1>

<form method="post" class="form-grid" style="background:white;padding:24px;border-radius:12px;border:1px solid var(--outline-variant);margin-bottom:24px;">
    <?= csrfField() ?>
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
    <input type="text" name="company_name" placeholder="Nazwa firmy" required value="<?= e($edit['company_name'] ?? '') ?>">
    <input type="text" name="vat_number" placeholder="NIP" value="<?= e($edit['vat_number'] ?? '') ?>">
    <input type="text" name="address" placeholder="Adres" value="<?= e($edit['address'] ?? '') ?>">
    <input type="email" name="contact_email" placeholder="E-mail" value="<?= e($edit['contact_email'] ?? '') ?>">
    <select name="billing_terms">
        <option value="prepayment" <?= ($edit['billing_terms'] ?? '') === 'prepayment' ? 'selected' : '' ?>>Przedpłata</option>
        <option value="net14" <?= ($edit['billing_terms'] ?? '') === 'net14' ? 'selected' : '' ?>>Net 14</option>
        <option value="net30" <?= ($edit['billing_terms'] ?? '') === 'net30' ? 'selected' : '' ?>>Net 30</option>
    </select>
    <select name="price_list_id">
        <option value="">— Cennik —</option>
        <?php foreach ($priceLists as $pl): ?>
        <option value="<?= (int) $pl['id'] ?>" <?= ($edit['price_list_id'] ?? '') == $pl['id'] ? 'selected' : '' ?>><?= e($pl['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-primary"><?= $edit ? 'Zapisz' : 'Dodaj klienta' ?></button>
</form>

<table class="data-table">
    <thead><tr><th>Firma</th><th>NIP</th><th>E-mail</th><th>Cennik</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($customers as $c): ?>
        <tr>
            <td><?= e($c['company_name']) ?></td>
            <td><?= e($c['vat_number'] ?? '—') ?></td>
            <td><?= e($c['contact_email'] ?? '—') ?></td>
            <td><?= e($c['price_list_name'] ?? '—') ?></td>
            <td><a href="?edit=<?= (int) $c['id'] ?>">Edytuj</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
