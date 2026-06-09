<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$productRepo = new ProductRepository($pdo);
$product = $productRepo->findById($id);

if (!$product || !(int) $product['is_active']) {
    flash('error', 'Produkt nie został znaleziony.');
    redirect(appUrl('/public/index.php'));
}

$priceListId = priceListIdForUser();
$price = $productRepo->getPrice($id, $priceListId);
$stock = $productRepo->getAvailableStock($id);

// Przygotowanie danych do wyświetlania cen (B2B vs B2C)
$isBusiness = (bool) customerId();
$displayPrice = $price;
$displayLabel = 'netto';
$subPrice = null;

if ($price !== null && !$isBusiness) {
    $displayPrice = grossFromNet($price);
    $displayLabel = 'brutto';
} elseif ($price !== null && $isBusiness) {
    $subPrice = grossFromNet($price);
}

// Pobieranie podobnych produktów z tej samej kategorii
$relatedProducts = [];
$catId = (int)($product['category_id'] ?? 0);
if ($catId > 0) {
    $allRelated = $productRepo->findByCategory($catId, 6);
    $relatedProducts = array_filter($allRelated, function($p) use ($id) {
        return (int)$p['id'] !== $id;
    });
    $relatedProducts = array_slice($relatedProducts, 0, 4);
}

$pageTitle = e($product['name']) . ' | MarketFlow';
$activeNav = '';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main class="page-container">
    <nav class="breadcrumb-nav">
        <a href="<?= e(appUrl('/public/index.php')) ?>">Strona główna</a>
        <span class="material-symbols-outlined">chevron_right</span>
        <a href="<?= e(appUrl('/public/category.php')) ?>">Katalog</a>
        <span class="material-symbols-outlined">chevron_right</span>
        <span><?= e($product['name']) ?></span>
    </nav>

    <div class="product-detail-grid">
        <!-- Sekcja Zdjęcia -->
        <div class="product-image-container">
            <img src="<?= e(productImageUrl($product)) ?>" alt="<?= e($product['name']) ?>" class="product-main-image">
        </div>

        <!-- Sekcja Informacji i Zakupu -->
        <div class="product-info-side">
            <header class="product-header">
                <span class="badge"><?= e(typeLabel($product['type'])) ?></span>
                <h1 class="font-display-lg"><?= e($product['name']) ?></h1>
                <div class="product-meta-row">
                    <span class="font-label-md">SKU: <strong><?= e($product['sku']) ?></strong></span>
                    <?php if ($product['type'] === 'electronic' && $product['warranty_months']): ?>
                        <span class="font-label-md">Gwarancja: <strong><?= (int) $product['warranty_months'] ?> mies.</strong></span>
                    <?php endif; ?>
                    <?php if ($product['type'] === 'food' && $product['expiration_date']): ?>
                        <span class="font-label-md">Data ważności: <strong><?= e($product['expiration_date']) ?></strong></span>
                    <?php endif; ?>
                </div>
            </header>

            <div class="product-buy-box">
                <?php if ($price !== null): ?>
                    <div class="price-section">
                        <p class="font-display-lg main-price"><?= e(formatMoney($displayPrice)) ?> <span class="font-body-md"><?= $displayLabel ?></span></p>
                        <?php if ($subPrice): ?>
                            <p class="font-body-md secondary-price">Brutto: <?= e(formatMoney($subPrice)) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="stock-status <?= $stock > 0 ? 'in-stock' : 'out-of-stock' ?>">
                    <span class="material-symbols-outlined">
                        <?= $stock > 0 ? 'check_circle' : 'error' ?>
                    </span>
                    <span class="font-body-md">
                        <?= $stock > 0 ? 'Dostępność: <strong>' . e((string)$stock) . ' ' . e($product['unit']) . '</strong>' : 'Produkt chwilowo niedostępny' ?>
                    </span>
                </div>

                <?php if ($price !== null && $stock > 0): ?>
                    <form method="post" action="<?= e(appUrl('/public/cart_action.php')) ?>" class="add-to-cart-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $id ?>">
                        <div class="quantity-input-wrapper">
                            <label for="quantity" class="font-label-sm">Ilość</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= (int) $stock ?>" step="1" class="product-quantity-input">
                        </div>
                        <button type="submit" class="checkout-btn">
                            <span class="material-symbols-outlined">shopping_cart</span>
                            Dodaj do koszyka
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($product['description']): ?>
    <section class="product-description-section">
        <h2 class="font-headline-md product-description-heading">Opis</h2>
        <p class="font-body-lg product-description-text"><?= nl2br(e($product['description'])) ?></p>
    </section>
    <?php endif; ?>

    <?php if (!empty($relatedProducts)): ?>
    <section class="related-products-section">
        <h2 class="font-headline-md related-heading">Podobne produkty</h2>
        <div class="products-grid related-grid">
            <?php foreach ($relatedProducts as $relProduct): 
                $relPrice = $productRepo->getPrice((int)$relProduct['id'], $priceListId);
                $relStock = $productRepo->getAvailableStock((int)$relProduct['id']);
            ?>
            <div class="product-card" onclick="location.href='<?= e(appUrl('/public/product.php?id=' . $relProduct['id'])) ?>'">
                <div class="product-image">
                    <img src="<?= e(productImageUrl($relProduct)) ?>" alt="<?= e($relProduct['name']) ?>">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?= e($relProduct['name']) ?></h3>
                    <p class="font-label-sm product-rel-meta"><?= e(typeLabel($relProduct['type'])) ?> · Stan: <?= e((string) $relStock) ?></p>
                    
                    <?php if ($relPrice !== null): ?>
                        <?php if ($isBusiness): ?>
                            <p class="product-price"><?= e(formatMoney($relPrice)) ?> <small style="font-size:12px;font-weight:400;">netto</small></p>
                        <?php else: ?>
                            <p class="product-price"><?= e(formatMoney(grossFromNet($relPrice))) ?> <small style="font-size:12px;font-weight:400;">brutto</small></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="product-price" style="color:var(--on-surface-variant);">Brak ceny</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
