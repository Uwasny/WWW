<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$productRepo = new ProductRepository($pdo);
$cart = new CartService($productRepo, priceListIdForUser());
$items = $cart->items();

$pageTitle = 'Twój Koszyk | MarketFlow';
$activeNav = 'cart';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main style="padding-top:96px;padding:32px;max-width:1280px;margin:0 auto;min-height:calc(100vh - 200px);">
    <h1 class="font-headline-lg" style="margin-bottom:32px;">Twój Koszyk</h1>

    <?php if ($items === []): ?>
        <p class="font-body-lg">Koszyk jest pusty. <a href="<?= e(appUrl('/public/index.php')) ?>">Przeglądaj produkty</a></p>
    <?php else: ?>
    <div class="cart-container">
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                </div>
                <div class="cart-item-details">
                    <div class="cart-item-header">
                        <div>
                            <h3 class="cart-item-name"><?= e($item['name']) ?></h3>
                            <p class="cart-item-category">SKU: <?= e($item['sku']) ?> · <?= e(typeLabel($item['type'])) ?></p>
                        </div>
                        <form method="post" action="<?= e(appUrl('/public/cart_action.php')) ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                            <button type="submit" class="cart-item-remove" title="Usuń">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                    <div class="cart-item-footer">
                        <form method="post" action="<?= e(appUrl('/public/cart_action.php')) ?>" class="quantity-control" style="display:flex;align-items:center;gap:8px;">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                            <span class="font-label-sm">Ilość:</span>
                            <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" style="width:60px;padding:6px;border:1px solid var(--outline-variant);border-radius:6px;">
                            <button type="submit" class="btn-secondary" style="padding:6px 12px;border-radius:6px;border:1px solid var(--outline-variant);background:white;cursor:pointer;">OK</button>
                        </form>
                        <div class="font-price-display" style="color:var(--primary);"><?= e(formatMoney($item['line_total'])) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <h2 class="font-headline-md">Podsumowanie</h2>
            <div class="summary-row subtotal">
                <span>Wartość netto</span>
                <span><?= e(formatMoney($cart->subtotal())) ?></span>
            </div>
            <div class="summary-row shipping">
                <span>VAT (<?= (int)(vatRate()*100) ?>%)</span>
                <span><?= e(formatMoney($cart->totalGross() - $cart->subtotal())) ?></span>
            </div>
            <div class="summary-total">
                <span>Razem brutto</span>
                <span class="price"><?= e(formatMoney($cart->totalGross())) ?></span>
            </div>
            <?php if (isLoggedIn() && userRole() === 'customer'): ?>
            <a href="<?= e(appUrl('/public/checkout.php')) ?>" class="checkout-btn" style="display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;">
                Do kasy <span class="material-symbols-outlined">arrow_forward</span>
            </a>
            <?php else: ?>
            <p style="font-size:14px;color:var(--on-surface-variant);margin-bottom:12px;">Aby złożyć zamówienie, zaloguj się jako klient B2B.</p>
            <a href="<?= e(appUrl('/public/login.php')) ?>" class="checkout-btn" style="display:block;text-align:center;text-decoration:none;">Zaloguj się</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
