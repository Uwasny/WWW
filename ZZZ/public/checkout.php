<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

requireLogin();
if (userRole() !== 'customer') {
    flash('error', 'Zamówienia mogą składać tylko klienci B2B.');
    redirect(appUrl('/public/cart.php'));
}

$customerId = customerId();
if (!$customerId) {
    flash('error', 'Brak powiązania z kontem klienta.');
    redirect(appUrl('/public/cart.php'));
}

$productRepo = new ProductRepository($pdo);
$cart = new CartService($productRepo, priceListIdForUser());

if ($cart->isEmpty()) {
    redirect(appUrl('/public/cart.php'));
}

$customerRepo = new CustomerRepository($pdo);
$customer = $customerRepo->findById($customerId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    try {
        $orderService = new OrderService(
            $pdo,
            $productRepo,
            new InventoryRepository($pdo),
            new AuditService(new AuditRepository($pdo))
        );
        $orderId = $orderService->placeOrder($customerId, (int) $_SESSION['user_id'], $cart->items());
        $cart->clear();
        flash('success', 'Zamówienie zostało złożone.');
        redirect(appUrl('/panel/order.php?id=' . $orderId));
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect(appUrl('/public/checkout.php'));
    }
}

$pageTitle = 'Kasa | MarketFlow';
$activeNav = 'cart';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main style="padding-top:96px;padding:32px;max-width:800px;margin:0 auto;">
    <h1 class="font-headline-lg" style="margin-bottom:24px;">Potwierdzenie zamówienia</h1>

    <div style="background:white;border:1px solid var(--outline-variant);border-radius:12px;padding:24px;margin-bottom:24px;">
        <h2 class="font-headline-md" style="margin-bottom:16px;">Dane firmy</h2>
        <p><strong><?= e($customer['company_name']) ?></strong></p>
        <p>NIP: <?= e($customer['vat_number'] ?? '—') ?></p>
        <p><?= e($customer['address'] ?? '') ?></p>
        <p>Warunki płatności: <?= e($customer['billing_terms']) ?></p>
    </div>

    <div style="background:white;border:1px solid var(--outline-variant);border-radius:12px;padding:24px;margin-bottom:24px;">
        <h2 class="font-headline-md" style="margin-bottom:16px;">Pozycje</h2>
        <?php foreach ($cart->items() as $item): ?>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--outline-variant);">
            <span><?= e($item['name']) ?> × <?= (int) $item['quantity'] ?></span>
            <span><?= e(formatMoney($item['line_total'])) ?></span>
        </div>
        <?php endforeach; ?>
        <div style="display:flex;justify-content:space-between;padding-top:16px;font-weight:700;font-size:18px;">
            <span>Razem brutto</span>
            <span style="color:var(--primary);"><?= e(formatMoney($cart->totalGross())) ?></span>
        </div>
    </div>

    <form method="post">
        <?= csrfField() ?>
        <button type="submit" class="checkout-btn" style="width:100%;border:none;cursor:pointer;">Złóż zamówienie</button>
        <a href="<?= e(appUrl('/public/cart.php')) ?>" style="display:block;text-align:center;margin-top:16px;color:var(--primary);">← Wróć do koszyka</a>
    </form>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>
