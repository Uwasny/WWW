<?php
$activeNav = $activeNav ?? '';
$searchQuery = $searchQuery ?? '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'MarketFlow') ?></title>
    <link rel="stylesheet" href="<?= e(appUrl('/public/assets/css/style.css')) ?>">
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand-group">
            <a href="<?= e(appUrl('/public/index.php')) ?>" class="navbar-logo">MarketFlow</a>
            <div class="navbar-links">
                <a href="<?= e(appUrl('/public/category.php')) ?>" class="<?= $activeNav === 'categories' ? 'active' : '' ?>">Kategorie</a>
            </div>
        </div>

        <form class="search-bar" action="<?= e(appUrl('/public/search.php')) ?>" method="get">
            <input type="text" name="q" placeholder="Szukaj produktów..." value="<?= e($searchQuery) ?>">
            <span class="material-symbols-outlined search-icon">search</span>
        </form>

        <div class="navbar-actions">
            <?php if (isLoggedIn()): ?>
                <?php if (userRole() === 'customer'): ?>
                    <a href="<?= e(appUrl('/panel/')) ?>" class="material-symbols-outlined" title="Panel klienta">account_circle</a>
                <?php elseif (userRole() === 'admin'): ?>
                    <a href="<?= e(appUrl('/admin/')) ?>" class="material-symbols-outlined" title="Panel admina">admin_panel_settings</a>
                <?php endif; ?>
                <a href="<?= e(appUrl('/public/logout.php')) ?>" class="material-symbols-outlined" title="Wyloguj">logout</a>
            <?php else: ?>
                <a href="<?= e(appUrl('/public/login.php')) ?>" class="material-symbols-outlined" title="Logowanie">account_circle</a>
            <?php endif; ?>
            <a href="<?= e(appUrl('/public/cart.php')) ?>" class="material-symbols-outlined" title="Koszyk">shopping_cart
                <?php if (cartCount() > 0): ?><span style="font-size:12px;">(<?= cartCount() ?>)</span><?php endif; ?>
            </a>
        </div>
    </div>
</nav>

<?php if ($msg = flash('success')): ?>
    <div style="max-width:1280px;margin:80px auto 0;padding:12px 24px;background:#e8f5e9;color:#2e7d32;border-radius:8px;"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
    <div style="max-width:1280px;margin:80px auto 0;padding:12px 24px;background:#ffebee;color:#c62828;border-radius:8px;"><?= e($msg) ?></div>
<?php endif; ?>
