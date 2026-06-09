<?php
function panelHeader(string $title, string $active = ''): void
{
    $user = currentUser();
    ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | MarketFlow</title>
    <link rel="stylesheet" href="<?= e(appUrl('/public/assets/css/style.css')) ?>">
    <style>
        .panel-wrap { display:flex; gap:32px; max-width:1280px; margin:96px auto 48px; padding:0 32px; min-height:70vh; }
        .panel-sidebar { width:260px; flex-shrink:0; }
        .panel-sidebar nav { background:var(--surface-container-low); border-radius:12px; padding:16px; border:1px solid var(--outline-variant); }
        .panel-sidebar a { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:8px; color:var(--on-surface-variant); text-decoration:none; margin-bottom:4px; }
        .panel-sidebar a.active, .panel-sidebar a:hover { background:var(--primary-fixed); color:var(--on-primary-fixed); }
        .panel-sidebar .disabled-link { display:flex; align-items:center; gap:12px; padding:12px 16px; color:var(--outline); cursor:not-allowed; opacity:0.6; }
        .panel-info-box { background: var(--surface-container-highest); padding: 24px; border-radius: 16px; margin-bottom: 32px; border: 1px dashed var(--primary); }
        .panel-info-box-content { display:flex; gap: 16px; align-items: center; }
        .panel-info-box-icon { font-size: 40px; color: var(--primary); }
        .panel-info-box-title { margin-bottom: 8px; }
        .panel-info-box-text a { color:var(--primary); font-weight:600; }
        .panel-sidebar .disabled-link { display:flex; align-items:center; gap:12px; padding:12px 16px; color:var(--outline); cursor:not-allowed; opacity:0.6; }
        .panel-content { flex:1; }
        .data-table { width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; }
        .data-table th, .data-table td { padding:12px 16px; text-align:left; border-bottom:1px solid var(--outline-variant); }
        .data-table th { background:var(--surface-container-high); font-weight:700; }
        .badge { padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600; background:var(--tertiary-fixed); color:var(--on-tertiary-fixed); }
        .order-card-panel { background:white; border:1px solid var(--outline-variant); border-radius:12px; padding:20px; margin-bottom:16px; }
        @media (max-width:768px) { .panel-wrap { flex-direction:column; } .panel-sidebar { width:100%; } }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?= e(appUrl('/public/index.php')) ?>" class="navbar-logo">MarketFlow</a>
        <div class="navbar-actions">
            <a href="<?= e(appUrl('/public/index.php')) ?>">Sklep</a>
            <a href="<?= e(appUrl('/panel/logout.php')) ?>">Wyloguj</a>
        </div>
    </div>
</nav>
<div class="panel-wrap">
    <aside class="panel-sidebar">
        <div class="panel-header-greeting">
            <h2 class="font-headline-md panel-header-greeting-title">Cześć, <?= e($user['username'] ?? 'Klient') ?>!</h2>
            <p class="font-body-md panel-header-greeting-subtitle">Panel klienta B2B</p>
        </div>
        <nav>
            <a href="<?= e(appUrl('/panel/')) ?>" class="<?= $active === 'dashboard' ? 'active' : '' ?>"><span class="material-symbols-outlined">dashboard</span> Pulpit</a>
            <a href="<?= e(appUrl('/panel/orders.php')) ?>" class="<?= $active === 'orders' ? 'active' : '' ?>"><span class="material-symbols-outlined">inventory_2</span> Zamówienia</a>
            
            <?php if (customerId()): ?>
                <a href="<?= e(appUrl('/panel/invoices.php')) ?>" class="<?= $active === 'invoices' ? 'active' : '' ?>"><span class="material-symbols-outlined">receipt_long</span> Faktury</a>
            <?php else: ?>
                <div class="disabled-link" title="Dostępne po uzupełnieniu danych firmy"><span class="material-symbols-outlined">receipt_long</span> Faktury</div>
            <?php endif; ?>

            <a href="<?= e(appUrl('/panel/account.php')) ?>" class="<?= $active === 'account' ? 'active' : '' ?>"><span class="material-symbols-outlined">business</span> Dane firmy</a>
        </nav>
    </aside>
    <main class="panel-content">
    <?php
}

function panelFooter(): void
{
    ?>
    </main>
</div>
</body>
</html>
    <?php
}
