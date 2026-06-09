<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$categoryRepo = new CategoryRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $name = trim($_POST['name'] ?? '');
    $parentId = (int) ($_POST['parent_id'] ?? 0) ?: null;

    if (!empty($_POST['id'])) {
        $id = (int) $_POST['id'];
        $categoryRepo->update($id, $name, $parentId);
        $audit->log('product_categories', $id, 'updated', ['name' => $name]);
    } else {
        $id = $categoryRepo->create($name, $parentId);
        $audit->log('product_categories', $id, 'created', ['name' => $name]);
    }
    flash('success', 'Kategoria zapisana.');
    redirect(appUrl('/admin/categories.php'));
}

if (isset($_GET['delete'])) {
    verifyCsrf($_GET['csrf_token'] ?? null);
    $categoryRepo->delete((int) $_GET['delete']);
    flash('success', 'Kategoria usunięta.');
    redirect(appUrl('/admin/categories.php'));
}

$categories = $categoryRepo->all();
adminHeader('Kategorie', 'categories');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Kategorie</h1>

<form method="post" class="form-grid" style="background:white;padding:24px;border-radius:12px;border:1px solid var(--outline-variant);margin-bottom:24px;">
    <?= csrfField() ?>
    <input type="text" name="name" placeholder="Nazwa kategorii" required>
    <select name="parent_id">
        <option value="">— Brak rodzica —</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-primary">Dodaj kategorię</button>
</form>

<table class="data-table">
    <thead><tr><th>ID</th><th>Nazwa</th><th>Rodzic ID</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td><?= (int) $c['id'] ?></td>
            <td><?= e($c['name']) ?></td>
            <td><?= e((string) ($c['parent_id'] ?? '—')) ?></td>
            <td><a href="?delete=<?= (int) $c['id'] ?>&csrf_token=<?= e(csrfToken()) ?>" onclick="return confirm('Usunąć?')">Usuń</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
