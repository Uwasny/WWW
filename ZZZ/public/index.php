<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$productRepo = new ProductRepository($pdo);
$categoryRepo = new CategoryRepository($pdo);
$priceListId = priceListIdForUser();

$products = $productRepo->findActive(24);
$categories = $categoryRepo->allWithProductCounts();

$pageTitle = 'MarketFlow | Odkrywaj i Kupuj';
$activeNav = 'home';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main>
    <section class="hero">
        <img src="https://via.placeholder.com/1400x500?text=MarketFlow+Magazyn+B2B" alt="Hero Banner">
        <div class="hero-overlay">
            <span class="hero-badge">Magazyn B2B</span>
            <h1 class="font-display-lg">Zamawiaj hurtowo i wygodnie</h1>
            <p class="font-body-lg">Elektronika, żywność, materiały budowlane i narzędzia — ceny dopasowane do Twojego cennika B2B.</p>
            <a href="<?= e(appUrl('/public/category.php')) ?>" class="hero-btn" style="display:inline-block;text-decoration:none;text-align:center;">Przeglądaj katalog</a>
        </div>
    </section>

    <section id="categories">
        <h2 class="font-headline-md">Przeglądaj kategorie</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $cat):
                $meta = categoryPresentation($cat['name']);
                $count = (int) ($cat['product_count'] ?? 0);
            ?>
            <a href="<?= e(appUrl('/public/category.php?category_id=' . $cat['id'])) ?>" class="category-card category-card--<?= e($meta['modifier']) ?>">
                <div class="category-card__icon">
                    <span class="material-symbols-outlined"><?= e($meta['icon']) ?></span>
                </div>
                <div>
                    <span class="category-card__name"><?= e($cat['name']) ?></span>
                    <span class="category-card__desc"><?= e($meta['description']) ?></span>
                </div>
                <span class="category-card__count"><?= e(productCountLabel($count)) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2 class="font-headline-md">Produkty w ofercie</h2>
        <div class="products-grid">
            <?php foreach ($products as $product):
                $price = $productRepo->getPrice((int) $product['id'], $priceListId);
                $stock = $productRepo->getAvailableStock((int) $product['id']);
            ?>
            <div class="product-card" onclick="location.href='<?= e(appUrl('/public/product.php?id=' . $product['id'])) ?>'">
                <div class="product-image">
                    <img src="<?= e(productImageUrl($product)) ?>" alt="<?= e($product['name']) ?>">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?= e($product['name']) ?></h3>
                    <p class="font-label-sm" style="color:var(--on-surface-variant);"><?= e(typeLabel($product['type'])) ?> · Stan: <?= e((string) $stock) ?></p>
                    <?php if ($price !== null): ?>
                    <p class="product-price"><?= e(formatMoney($price)) ?></p>
                    <?php else: ?>
                    <p class="product-price" style="color:var(--on-surface-variant);">Brak ceny</p>
                    <?php endif; ?>
                    <?php if ($price !== null && $stock > 0): ?>
                    <form method="post" action="<?= e(appUrl('/public/cart_action.php')) ?>" onclick="event.stopPropagation();">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="product-button">Dodaj do koszyka</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
