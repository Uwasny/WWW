<?php

declare(strict_types=1);

class CartService
{
    public function __construct(
        private ProductRepository $products,
        private int $priceListId
    ) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function items(): array
    {
        $result = [];
        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $this->products->findById((int) $productId);
            if (!$product) {
                continue;
            }
            $price = $this->products->getPrice((int) $productId, $this->priceListId) ?? 0.0;
            $qty = (float) $item['quantity'];
            $result[] = [
                'product_id' => (int) $productId,
                'name' => $product['name'],
                'sku' => $product['sku'],
                'type' => $product['type'],
                'category_name' => $product['category_name'] ?? '',
                'quantity' => $qty,
                'unit_price' => $price,
                'line_total' => round($price * $qty, 2),
                'image' => productImageUrl($product),
            ];
        }
        return $result;
    }

    public function add(int $productId, float $quantity = 1): void
    {
        if ($quantity <= 0) {
            return;
        }
        $current = $_SESSION['cart'][$productId]['quantity'] ?? 0;
        $_SESSION['cart'][$productId] = ['quantity' => $current + $quantity];
    }

    public function update(int $productId, float $quantity): void
    {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            return;
        }
        $_SESSION['cart'][$productId] = ['quantity' => $quantity];
    }

    public function remove(int $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function subtotal(): float
    {
        return array_sum(array_column($this->items(), 'line_total'));
    }

    public function totalGross(): float
    {
        return grossFromNet($this->subtotal());
    }

    public function isEmpty(): bool
    {
        return $this->items() === [];
    }
}
