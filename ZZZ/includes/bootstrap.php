<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__) . '/config/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/repositories/ProductRepository.php';
require_once __DIR__ . '/repositories/CategoryRepository.php';
require_once __DIR__ . '/repositories/OrderRepository.php';
require_once __DIR__ . '/repositories/CustomerRepository.php';
require_once __DIR__ . '/repositories/InventoryRepository.php';
require_once __DIR__ . '/repositories/InvoiceRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/repositories/AuditRepository.php';
require_once __DIR__ . '/repositories/AdminRepository.php';
require_once __DIR__ . '/services/AuditService.php';
require_once __DIR__ . '/services/CartService.php';
require_once __DIR__ . '/services/OrderService.php';
require_once __DIR__ . '/seed.php';
require_once __DIR__ . '/migrations.php';
require_once __DIR__ . '/product_image.php';
require_once __DIR__ . '/auth.php';

$pdo = db();
ensureSchema($pdo);
ensureSeedData($pdo);

function appUrl(string $path = ''): string
{
    $base = rtrim((string) appConfig('APP_URL', ''), '/');
    return $base . $path;
}
