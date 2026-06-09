<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$query = trim($_GET['q'] ?? '');
$productRepo = new ProductRepository($pdo);
$priceListId = priceListIdForUser();
$isBusiness = (bool) customerId(); // Check if user is a business customer
$products = $query !== '' ? $productRepo->search($query) : [];

$pageTitle = 'Szukaj | MarketFlow';
$searchQuery = $query;
$activeNav = '';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main class="search-main-content">
    <h1 class="font-headline-lg search-heading">Wyniki wyszukiwania<?= $query ? ': „' . e($query) . '”' : '' ?></h1>

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
                        <?php if ($isBusiness): ?>
                            <p class="product-price"><?= e(formatMoney($price)) ?> <small class="product-price-small-label">netto</small></p>
                        <?php else: ?>
                            <p class="product-price"><?= e(formatMoney(grossFromNet($price))) ?> <small class="product-price-small-label">brutto</small></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
