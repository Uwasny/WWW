<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('customer');
require_once dirname(__DIR__) . '/includes/layout/panel_layout.php';

$customerId = customerId();
$orderRepo = new OrderRepository($pdo);
$orders = $customerId ? $orderRepo->findByCustomer((int)$customerId, 5) : [];

panelHeader('Pulpit', 'dashboard');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Pulpit klienta</h1>

<?php if (!$customerId): ?>
    <div style="background: var(--surface-container-highest); padding: 24px; border-radius: 16px; margin-bottom: 32px; border: 1px dashed var(--primary);">
        <div style="display:flex; gap: 16px; align-items: center;">
            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--primary);">business</span>
            <div>
                <h3 class="font-headline-md">Kupuj jako firma</h3>
                <p class="font-body-md">Uzupełnij dane o firmie w ustawieniach profilu. Po zatwierdzeniu przez administratora otrzymasz dostęp do cen hurtowych netto.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<p class="font-body-md" style="margin-bottom:32px;color:var(--on-surface-variant);">Twoje ostatnie zamówienia</p>

<?php if ($orders === []): ?>
    <p>Brak zamówień. <a href="<?= e(appUrl('/public/index.php')) ?>">Przejdź do sklepu</a></p>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
    <div class="order-card-panel">
        <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:12px;">
            <div>
                <p class="font-label-sm" style="color:var(--on-surface-variant);">ZAMÓWIENIE</p>
                <p class="font-headline-md"><?= e($order['order_number']) ?></p>
            </div>
            <div>
                <p class="font-label-sm" style="color:var(--on-surface-variant);">DATA</p>
                <p><?= e(date('d.m.Y', strtotime($order['created_at']))) ?></p>
            </div>
            <div>
                <p class="font-label-sm" style="color:var(--on-surface-variant);">SUMA</p>
                <p class="font-price-display"><?= e(formatMoney((float) $order['total_gross'])) ?></p>
            </div>
            <span class="badge"><?= e(orderStatusLabel($order['status'])) ?></span>
        </div>
        <a href="<?= e(appUrl('/panel/order.php?id=' . $order['id'])) ?>" style="color:var(--primary);font-weight:600;">Szczegóły →</a>
    </div>
    <?php endforeach; ?>
    <a href="<?= e(appUrl('/panel/orders.php')) ?>" style="color:var(--primary);">Zobacz wszystkie zamówienia</a>
<?php endif; ?>

<?php panelFooter(); ?>
