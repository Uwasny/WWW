<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('customer');
require_once dirname(__DIR__) . '/includes/layout/panel_layout.php';

$customerId = customerId();
$customerRepo = new CustomerRepository($pdo);
$customer = $customerRepo->findById($customerId);

panelHeader('Dane firmy', 'account');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Dane firmy</h1>

<div class="order-card-panel">
    <p><strong>Nazwa firmy:</strong> <?= e($customer['company_name']) ?></p>
    <p><strong>NIP:</strong> <?= e($customer['vat_number'] ?? '—') ?></p>
    <p><strong>Adres:</strong> <?= e($customer['address'] ?? '—') ?></p>
    <p><strong>E-mail kontaktowy:</strong> <?= e($customer['contact_email'] ?? '—') ?></p>
    <p><strong>Warunki płatności:</strong> <?= e($customer['billing_terms']) ?></p>
    <p><strong>Cennik:</strong> <?= e($customer['price_list_name'] ?? 'Domyślny') ?></p>
    <p><strong>Konto od:</strong> <?= e(date('d.m.Y', strtotime($customer['created_at']))) ?></p>
</div>

<?php panelFooter(); ?>
