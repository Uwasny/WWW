<?php



require_once dirname(__DIR__) . '/includes/bootstrap.php';

requireRole('admin');

require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';



$productRepo = new ProductRepository($pdo);

$categoryRepo = new CategoryRepository($pdo);

$audit = new AuditService(new AuditRepository($pdo));



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifyCsrf($_POST['csrf_token'] ?? null);



    $isEdit = !empty($_POST['id']);

    $id = $isEdit ? (int) $_POST['id'] : 0;

    $existing = $isEdit ? $productRepo->findById($id) : null;



    if ($isEdit && !$existing) {

        flash('error', 'Produkt nie został znaleziony.');

        redirect(appUrl('/admin/products.php'));

    }



    $imagePath = $existing['image_path'] ?? null;



    try {

        if ($isEdit && !empty($_POST['remove_image']) && $imagePath) {

            deleteProductImageFile($imagePath);

            $imagePath = null;

        }



        if ($isEdit && !empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {

            $imagePath = saveProductImage($id, $_FILES['image'], $imagePath);

        }

    } catch (Throwable $e) {

        flash('error', $e->getMessage());

        redirect(appUrl('/admin/products.php' . ($isEdit ? '?edit=' . $id : '')));

    }



    $data = [

        'sku' => trim($_POST['sku'] ?? ''),

        'name' => trim($_POST['name'] ?? ''),

        'description' => trim($_POST['description'] ?? ''),

        'image_path' => $imagePath,

        'category_id' => (int) ($_POST['category_id'] ?? 0) ?: null,

        'unit' => trim($_POST['unit'] ?? 'szt'),

        'is_active' => isset($_POST['is_active']) ? 1 : 0,

        'type' => $_POST['type'] ?? 'electronic',

        'warranty_months' => $_POST['warranty_months'] !== '' ? (int) $_POST['warranty_months'] : null,

        'expiration_date' => $_POST['expiration_date'] !== '' ? $_POST['expiration_date'] : null,

    ];



    if ($isEdit) {

        $productRepo->update($id, $data);

        $audit->log('products', $id, 'updated', array_merge($data, ['image_changed' => isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']]));

        flash('success', 'Produkt zaktualizowany.');

        redirect(appUrl('/admin/products.php?edit=' . $id));

    }



    $newId = $productRepo->create($data);



    if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {

        try {

            $path = saveProductImage($newId, $_FILES['image'], null);

            $data['image_path'] = $path;

            $productRepo->updateImagePath($newId, $path);

        } catch (Throwable $e) {

            flash('success', 'Produkt dodany, ale zdjęcie nie zostało zapisane: ' . $e->getMessage());

            redirect(appUrl('/admin/products.php?edit=' . $newId));

        }

    }



    $audit->log('products', $newId, 'created', $data);

    flash('success', 'Produkt dodany.');

    redirect(appUrl('/admin/products.php?edit=' . $newId));

}



$products = $productRepo->all();

$categories = $categoryRepo->all();

$edit = null;

if (!empty($_GET['edit'])) {

    $edit = $productRepo->findById((int) $_GET['edit']);

}



adminHeader('Produkty', 'products');

?>



<h1 class="font-headline-lg" style="margin-bottom:24px;">Produkty</h1>



<form method="post" enctype="multipart/form-data" class="form-grid" style="background:white;padding:24px;border-radius:12px;border:1px solid var(--outline-variant);margin-bottom:32px;">

    <?= csrfField() ?>

    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>

    <input type="text" name="sku" placeholder="SKU" required value="<?= e($edit['sku'] ?? '') ?>">

    <input type="text" name="name" placeholder="Nazwa" required value="<?= e($edit['name'] ?? '') ?>">

    <textarea name="description" placeholder="Opis" rows="2"><?= e($edit['description'] ?? '') ?></textarea>



    <div style="display:flex;flex-direction:column;gap:12px;padding:16px;border:1px dashed var(--outline-variant);border-radius:8px;">

        <label class="font-label-md" for="image">Zdjęcie produktu</label>

        <?php if ($edit && !empty($edit['image_path'])): ?>

            <img src="<?= e(productImageUrl($edit)) ?>" alt="Podgląd" style="max-width:200px;max-height:200px;object-fit:cover;border-radius:8px;border:1px solid var(--outline-variant);">

            <label style="display:flex;align-items:center;gap:8px;font-size:14px;">

                <input type="checkbox" name="remove_image" value="1"> Usuń obecne zdjęcie

            </label>

        <?php endif; ?>

        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">

        <span style="font-size:12px;color:var(--on-surface-variant);">JPG, PNG, GIF lub WEBP, maks. 5 MB</span>

    </div>



    <select name="category_id">

        <option value="">— Kategoria —</option>

        <?php foreach ($categories as $c): ?>

        <option value="<?= (int) $c['id'] ?>" <?= ($edit['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>

        <?php endforeach; ?>

    </select>

    <select name="type">

        <option value="electronic" <?= ($edit['type'] ?? '') === 'electronic' ? 'selected' : '' ?>>Elektronika</option>

        <option value="food" <?= ($edit['type'] ?? '') === 'food' ? 'selected' : '' ?>>Żywność</option>

        <option value="building" <?= ($edit['type'] ?? '') === 'building' ? 'selected' : '' ?>>Materiały budowlane</option>

        <option value="tools" <?= ($edit['type'] ?? '') === 'tools' ? 'selected' : '' ?>>Narzędzia</option>

    </select>

    <input type="text" name="unit" placeholder="Jednostka" value="<?= e($edit['unit'] ?? 'szt') ?>">

    <input type="number" name="warranty_months" placeholder="Gwarancja (mies.)" value="<?= e((string) ($edit['warranty_months'] ?? '')) ?>">

    <input type="date" name="expiration_date" value="<?= e($edit['expiration_date'] ?? '') ?>">

    <label><input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Aktywny</label>

    <div style="display:flex;gap:12px;flex-wrap:wrap;">

        <button type="submit" class="btn-primary"><?= $edit ? 'Zapisz' : 'Dodaj produkt' ?></button>

        <?php if ($edit): ?>

        <a href="<?= e(appUrl('/admin/products.php')) ?>" class="btn-secondary">Anuluj edycję</a>

        <?php endif; ?>

    </div>

</form>



<table class="data-table">

    <thead><tr><th>Zdjęcie</th><th>ID</th><th>SKU</th><th>Nazwa</th><th>Typ</th><th>Kategoria</th><th>Aktywny</th><th></th></tr></thead>

    <tbody>

        <?php foreach ($products as $p): ?>

        <tr>

            <td>

                <img src="<?= e(productImageUrl($p)) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">

            </td>

            <td><?= (int) $p['id'] ?></td>

            <td><?= e($p['sku']) ?></td>

            <td><?= e($p['name']) ?></td>

            <td><?= e(typeLabel($p['type'])) ?></td>

            <td><?= e($p['category_name'] ?? '—') ?></td>

            <td><?= (int) $p['is_active'] ? 'Tak' : 'Nie' ?></td>

            <td><a href="?edit=<?= (int) $p['id'] ?>">Edytuj</a></td>

        </tr>

        <?php endforeach; ?>

    </tbody>

</table>



<?php adminFooter(); ?>


