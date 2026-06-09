<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function appConfig(string $key, mixed $default = null): mixed
{
    static $env = null;
    if ($env === null) {
        $root = dirname(__DIR__);
        $env = loadEnv($root . '/.env');
        if ($env === []) {
            $env = loadEnv($root . '/.env.example');
        }
    }

    return $env[$key] ?? $default;
}

function formatMoney(float $amount): string
{
    return number_format($amount, 2, ',', ' ') . ' zł';
}

function vatRate(): float
{
    return (float) appConfig('VAT_RATE', '0.23');
}

function grossFromNet(float $net): float
{
    return round($net * (1 + vatRate()), 2);
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
}

function typeLabel(string $type): string
{
    return match ($type) {
        'electronic' => 'Elektronika',
        'food' => 'Żywność',
        'building' => 'Materiały budowlane',
        'tools' => 'Narzędzia',
        default => ucfirst($type),
    };
}

/** @return array{icon: string, modifier: string, description: string} */
function categoryPresentation(string $name): array
{
    $key = mb_strtolower(trim($name));

    return match ($key) {
        'elektronika' => [
            'icon' => 'laptop_mac',
            'modifier' => 'electronic',
            'description' => 'Sprzęt IT, audio i akcesoria biurowe',
        ],
        'żywność', 'zywnosc' => [
            'icon' => 'restaurant',
            'modifier' => 'food',
            'description' => 'Artykuły spożywcze i napoje',
        ],
        'materiały budowlane', 'materialy budowlane' => [
            'icon' => 'foundation',
            'modifier' => 'building',
            'description' => 'Cement, pustaki, izolacja i chemia budowlana',
        ],
        'narzędzia', 'narzedzia' => [
            'icon' => 'handyman',
            'modifier' => 'tools',
            'description' => 'Elektronarzędzia i narzędzia ręczne',
        ],
        default => [
            'icon' => 'category',
            'modifier' => 'default',
            'description' => 'Produkty z magazynu B2B',
        ],
    };
}

function orderStatusLabel(string $status): string
{
    return match ($status) {
        'NEW', 'CREATED' => 'Nowe',
        'CONFIRMED' => 'Potwierdzone',
        'SHIPPED' => 'Wysłane',
        'COMPLETED' => 'Zrealizowane',
        'CANCELLED' => 'Anulowane',
        default => $status,
    };
}

function invoiceStatusLabel(string $status): string
{
    return match ($status) {
        'UNPAID' => 'Nieopłacona',
        'PAID' => 'Opłacona',
        default => $status,
    };
}

function productCountLabel(int $count): string
{
    if ($count === 1) {
        return '1 produkt';
    }

    $mod10 = $count % 10;
    $mod100 = $count % 100;
    if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14)) {
        return $count . ' produkty';
    }

    return $count . ' produktów';
}
function generateInvoiceNumber(): string
{
    return 'FV-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

function productPlaceholderImage(string $name): string
{
    $text = rawurlencode(mb_substr($name, 0, 24));
    return 'https://via.placeholder.com/400x400?text=' . $text;
}

function productImageUrl(array $product): string
{
    $path = $product['image_path'] ?? null;
    if ($path) {
        return appUrl('/public/' . ltrim($path, '/'));
    }

    return productPlaceholderImage($product['name'] ?? 'Produkt');
}

function cartCount(): int
{
    $cart = $_SESSION['cart'] ?? [];
    $count = 0;
    foreach ($cart as $item) {
        $count += (int) ($item['quantity'] ?? 0);
    }
    return $count;
}
function generateOrderNumber(): string
{
    $datePart = date('Ymd');
    // Generujemy 4 losowe znaki szesnastkowe dla unikalności w obrębie dnia
    $randomPart = strtoupper(bin2hex(random_bytes(2))); 
    
    return "ORD-{$datePart}-{$randomPart}";
}
