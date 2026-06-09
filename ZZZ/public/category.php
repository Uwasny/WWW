<?php



require_once dirname(__DIR__) . '/includes/bootstrap.php';



$productRepo = new ProductRepository($pdo);

$categoryRepo = new CategoryRepository($pdo);

$priceListId = priceListIdForUser();



$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;

$category = $categoryId ? $categoryRepo->findById($categoryId) : null;



$products = $productRepo->findByCategory($categoryId ?: null, null);

$categories = $categoryRepo->allWithProductCounts();



$title = $category['name'] ?? 'Wszystkie produkty';

$activeMeta = $category ? categoryPresentation($category['name']) : null;

$pageTitle = $title . ' | MarketFlow';

$activeNav = 'categories';

require dirname(__DIR__) . '/includes/layout/storefront_header.php';

?>

<main class="page-container">
    <div class="category-hero">

        <?php if ($activeMeta): ?>
            <span class="material-symbols-outlined category-hero-icon"><?= e($activeMeta['icon']) ?></span>
        <?php endif; ?>

        <h1 class="font-display-lg"><?= e($title) ?></h1>
        <p class="font-body-lg"><?= e(productCountLabel(count($products))) ?> w kategorii</p>

        <?php if ($activeMeta): ?>
            <p class="font-body-md"><?= e($activeMeta['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="category-layout">
        <aside class="sidebar category-sidebar">

            <strong class="category-sidebar__title">Kategorie</strong>

            <div class="category-filters">

                <a href="<?= e(appUrl('/public/category.php')) ?>" class="category-filter <?= !$categoryId ? 'is-active' : '' ?>">

                    <span class="material-symbols-outlined category-filter__icon">apps</span>

                    Wszystkie

                </a>

                <?php foreach ($categories as $cat):

                    $meta = categoryPresentation($cat['name']);

                ?>

                <a href="<?= e(appUrl('/public/category.php?category_id=' . $cat['id'])) ?>"

                   class="category-filter <?= $categoryId === (int) $cat['id'] ? 'is-active' : '' ?>">

                    <span class="material-symbols-outlined category-filter__icon"><?= e($meta['icon']) ?></span>

                    <?= e($cat['name']) ?>

                </a>

                <?php endforeach; ?>

            </div>

        </aside>



        <div style="flex:1;min-width:0;">

            <?php if ($products === []): ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <p class="font-body-lg">Brak produktów w tej kategorii.</p>
                    <a href="<?= e(appUrl('/public/category.php')) ?>" style="color:var(--primary);font-weight:600;">Zobacz wszystkie produkty</a>
                </div>
            <?php else: ?>
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

                            <p class="font-label-sm" style="color:var(--on-surface-variant);"><?= e($product['category_name'] ?? typeLabel($product['type'])) ?> · Stan: <?= e((string) $stock) ?></p>

                            <?php if ($price !== null): ?>

                                <?php if (customerId()): ?>
                                    <p class="product-price"><?= e(formatMoney($price)) ?> <small style="font-size:12px;font-weight:400;">netto</small></p>
                                <?php else: ?>
                                    <p class="product-price"><?= e(formatMoney(grossFromNet($price))) ?> <small style="font-size:12px;font-weight:400;">brutto</small></p>
                                <?php endif; ?>

                            <?php else: ?>

                            <p class="product-price" style="color:var(--on-surface-variant);">Brak ceny</p>

                            <?php endif; ?>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>



<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
