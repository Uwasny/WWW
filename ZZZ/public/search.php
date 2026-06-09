<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$query = trim($_GET['q'] ?? '');
$productRepo = new ProductRepository($pdo);
$priceListId = priceListIdForUser();
$products = $query !== '' ? $productRepo->search($query) : [];

$pageTitle = 'Szukaj | MarketFlow';
$searchQuery = $query;
$activeNav = '';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main style="padding-top:96px;padding:32px;max-width:1280px;margin:0 auto;">
    <h1 class="font-headline-lg" style="margin-bottom:24px;">Wyniki wyszukiwania<?= $query ? ': „' . e($query) . '”' : '' ?></h1>

    <?php if ($query === ''): ?>
        <p>Wpisz frazę w pasku wyszukiwania.</p>
    <?php elseif ($products === []): ?>
        <p>Brak produktów pasujących do zapytania.</p>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product):
                $price = $productRepo->getPrice((int) $product['id'], $priceListId);
            ?>
            <div class="product-card" onclick="location.href='<?= e(appUrl('/public/product.php?id=' . $product['id'])) ?>'">
                <div class="product-image">
                    <img src="<?= e(productImageUrl($product)) ?>" alt="<?= e($product['name']) ?>">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?= e($product['name']) ?></h3>
                    <?php if ($price !== null): ?>
                    <p class="product-price"><?= e(formatMoney($price)) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
