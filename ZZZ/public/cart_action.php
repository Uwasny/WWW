<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(appUrl('/public/cart.php'));
}

verifyCsrf($_POST['csrf_token'] ?? null);

$productRepo = new ProductRepository($pdo);
$cart = new CartService($productRepo, priceListIdForUser());
$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);

switch ($action) {
    case 'add':
        $qty = max(1, (float) ($_POST['quantity'] ?? 1));
        $cart->add($productId, $qty);
        flash('success', 'Produkt dodany do koszyka.');
        $redirect = $_POST['redirect'] ?? appUrl('/public/cart.php');
        redirect($redirect);
    case 'update':
        $cart->update($productId, (float) ($_POST['quantity'] ?? 1));
        redirect(appUrl('/public/cart.php'));
    case 'remove':
        $cart->remove($productId);
        redirect(appUrl('/public/cart.php'));
    default:
        redirect(appUrl('/public/cart.php'));
}
