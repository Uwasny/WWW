<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$adminRepo = new AdminRepository($pdo);
$productRepo = new ProductRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    if (isset($_POST['new_list'])) {
        $id = $adminRepo->createPriceList(trim($_POST['list_name']), $_POST['currency'] ?? 'PLN');
        $audit->log('price_lists', $id, 'created', ['name' => $_POST['list_name']]);
        flash('success', 'Cennik utworzony.');
    } else {
        $adminRepo->setProductPrice((int) $_POST['product_id'], (int) $_POST['price_list_id'], (float) $_POST['price']);
        $audit->log('product_prices', (int) $_POST['product_id'], 'updated', ['price' => $_POST['price']]);
        flash('success', 'Cena zapisana.');
    }
    redirect(appUrl('/admin/prices.php?list=' . (int) ($_POST['price_list_id'] ?? $_GET['list'] ?? 1)));
}

$lists = $adminRepo->priceLists();
$listId = (int) ($_GET['list'] ?? ($lists[0]['id'] ?? 1));
$prices = $adminRepo->productPrices($listId);
$products = $productRepo->all();

adminHeader('Cenniki', 'prices');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Cenniki</h1>

<div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
    <?php foreach ($lists as $list): ?>
    <a href="?list=<?= (int) $list['id'] ?>" class="btn-secondary" style="<?= $listId === (int)$list['id'] ? 'background:var(--primary-fixed);' : '' ?>"><?= e($list['name']) ?> (<?= e($list['currency']) ?>)</a>
    <?php endforeach; ?>
</div>

<form method="post" class="form-grid" style="background:white;padding:16px;border-radius:12px;margin-bottom:16px;max-width:400px;">
    <?= csrfField() ?>
    <input type="hidden" name="new_list" value="1">
    <input type="text" name="list_name" placeholder="Nazwa nowego cennika" required>
    <input type="text" name="currency" value="PLN" placeholder="Waluta">
    <button type="submit" class="btn-primary">Utwórz cennik</button>
</form>

<form method="post" class="form-grid" style="background:white;padding:16px;border-radius:12px;margin-bottom:24px;max-width:500px;">
    <?= csrfField() ?>
    <input type="hidden" name="price_list_id" value="<?= $listId ?>">
    <select name="product_id" required>
        <option value="">— Produkt —</option>
        <?php foreach ($products as $p): ?>
        <option value="<?= (int) $p['id'] ?>"><?= e($p['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" step="0.01" name="price" placeholder="Cena netto" required>
    <button type="submit" class="btn-primary">Ustaw cenę</button>
</form>

<table class="data-table">
    <thead><tr><th>Produkt</th><th>SKU</th><th>Cena netto</th><th>Min. ilość</th></tr></thead>
    <tbody>
        <?php foreach ($prices as $row): ?>
        <tr>
            <td><?= e($row['product_name']) ?></td>
            <td><?= e($row['sku']) ?></td>
            <td><?= e(formatMoney((float) $row['price'])) ?></td>
            <td><?= e((string) $row['min_quantity']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
