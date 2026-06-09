<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$adminRepo = new AdminRepository($pdo);
$customerRepo = new CustomerRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);

    $filename = $_FILES['csv_file']['name'] ?? 'upload.csv';
    $customerId = (int) ($_POST['customer_id'] ?? 0) ?: null;
    $status = 'PENDING';

    if (!empty($_FILES['csv_file']['tmp_name']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $status = 'COMPLETED';
        $id = $adminRepo->recordCsvImport($customerId, $filename, $status);
        $audit->log('csv_imports', $id, 'uploaded', ['filename' => $filename]);
        flash('success', 'Plik CSV zarejestrowany (import MVP — bez parsera).');
    } else {
        flash('error', 'Nie wybrano pliku.');
    }
    redirect(appUrl('/admin/imports.php'));
}

$imports = $adminRepo->csvImports();
$customers = $customerRepo->all();

adminHeader('Import CSV', 'imports');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Import CSV</h1>

<form method="post" enctype="multipart/form-data" class="form-grid" style="background:white;padding:24px;border-radius:12px;margin-bottom:24px;max-width:500px;">
    <?= csrfField() ?>
    <select name="customer_id">
        <option value="">— Klient (opcjonalnie) —</option>
        <?php foreach ($customers as $c): ?>
        <option value="<?= (int) $c['id'] ?>"><?= e($c['company_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="file" name="csv_file" accept=".csv" required>
    <button type="submit" class="btn-primary">Prześlij CSV</button>
</form>

<table class="data-table">
    <thead><tr><th>ID</th><th>Plik</th><th>Klient</th><th>Status</th><th>Data</th></tr></thead>
    <tbody>
        <?php foreach ($imports as $row): ?>
        <tr>
            <td><?= (int) $row['id'] ?></td>
            <td><?= e($row['filename']) ?></td>
            <td><?= e($row['company_name'] ?? '—') ?></td>
            <td><?= e($row['status']) ?></td>
            <td><?= e(date('d.m.Y H:i', strtotime($row['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($imports === []): ?><p style="margin-top:16px;">Brak importów.</p><?php endif; ?>

<?php adminFooter(); ?>
