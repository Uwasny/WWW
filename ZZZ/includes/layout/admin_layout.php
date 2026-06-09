<?php
function adminHeader(string $title, string $active = ''): void
{
    ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | Admin MarketFlow</title>
    <link rel="stylesheet" href="<?= e(appUrl('/public/assets/css/style.css')) ?>">
    <style>
        .admin-wrap { display:flex; gap:24px; max-width:1400px; margin:96px auto 48px; padding:0 24px; min-height:70vh; }
        .admin-sidebar { width:240px; flex-shrink:0; background:var(--inverse-surface); color:var(--inverse-on-surface); border-radius:12px; padding:16px; }
        .admin-sidebar a { display:block; padding:10px 14px; border-radius:8px; color:var(--inverse-on-surface); text-decoration:none; font-size:14px; margin-bottom:2px; }
        .admin-sidebar a.active, .admin-sidebar a:hover { background:rgba(255,255,255,0.12); }
        .admin-content { flex:1; }
        .stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:32px; }
        .stat-card { background:white; border:1px solid var(--outline-variant); border-radius:12px; padding:20px; }
        .stat-card h3 { font-size:28px; margin:8px 0 0; color:var(--primary); }
        .data-table { width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; margin-top:16px; }
        .data-table th, .data-table td { padding:10px 14px; text-align:left; border-bottom:1px solid var(--outline-variant); font-size:14px; }
        .data-table th { background:var(--surface-container-high); }
        .form-grid { display:grid; gap:12px; max-width:600px; }
        .form-grid input, .form-grid select, .form-grid textarea { padding:10px 14px; border:1px solid var(--outline-variant); border-radius:8px; font-family:inherit; }
        .btn-primary { background:var(--primary); color:white; border:none; padding:10px 20px; border-radius:999px; font-weight:700; cursor:pointer; }
        .btn-secondary { background:transparent; border:1px solid var(--outline-variant); padding:8px 16px; border-radius:999px; cursor:pointer; text-decoration:none; color:inherit; display:inline-block; }
        @media (max-width:900px) { .admin-wrap { flex-direction:column; } .admin-sidebar { width:100%; } }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?= e(appUrl('/admin/')) ?>" class="navbar-logo">MarketFlow Admin</a>
        <div class="navbar-actions">
            <a href="<?= e(appUrl('/public/index.php')) ?>">Sklep</a>
            <a href="<?= e(appUrl('/public/logout.php')) ?>">Wyloguj</a>
        </div>
    </div>
</nav>
<div class="admin-wrap">
    <aside class="admin-sidebar">
        <strong style="display:block;padding:8px 14px 16px;">Administracja</strong>
        <a href="<?= e(appUrl('/admin/')) ?>" class="<?= $active === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="<?= e(appUrl('/admin/products.php')) ?>" class="<?= $active === 'products' ? 'active' : '' ?>">Produkty</a>
        <a href="<?= e(appUrl('/admin/categories.php')) ?>" class="<?= $active === 'categories' ? 'active' : '' ?>">Kategorie</a>
        <a href="<?= e(appUrl('/admin/inventory.php')) ?>" class="<?= $active === 'inventory' ? 'active' : '' ?>">Stany magazynowe</a>
        <a href="<?= e(appUrl('/admin/warehouses.php')) ?>" class="<?= $active === 'warehouses' ? 'active' : '' ?>">Magazyny</a>
        <a href="<?= e(appUrl('/admin/prices.php')) ?>" class="<?= $active === 'prices' ? 'active' : '' ?>">Cenniki</a>
        <a href="<?= e(appUrl('/admin/customers.php')) ?>" class="<?= $active === 'customers' ? 'active' : '' ?>">Klienci</a>
        <a href="<?= e(appUrl('/admin/orders.php')) ?>" class="<?= $active === 'orders' ? 'active' : '' ?>">Zamówienia</a>
        <a href="<?= e(appUrl('/admin/invoices.php')) ?>" class="<?= $active === 'invoices' ? 'active' : '' ?>">Faktury</a>
        <a href="<?= e(appUrl('/admin/users.php')) ?>" class="<?= $active === 'users' ? 'active' : '' ?>">Użytkownicy</a>
        <a href="<?= e(appUrl('/admin/imports.php')) ?>" class="<?= $active === 'imports' ? 'active' : '' ?>">Import CSV</a>
        <a href="<?= e(appUrl('/admin/audit.php')) ?>" class="<?= $active === 'audit' ? 'active' : '' ?>">Logi audytu</a>
    </aside>
    <main class="admin-content">
    <?php if ($msg = flash('success')): ?><div style="padding:12px;background:#e8f5e9;border-radius:8px;margin-bottom:16px;"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div style="padding:12px;background:#ffebee;border-radius:8px;margin-bottom:16px;"><?= e($msg) ?></div><?php endif; ?>
    <?php
}

function adminFooter(): void
{
    ?>
    </main>
</div>
</body>
</html>
    <?php
}
