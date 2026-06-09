<?php

declare(strict_types=1);

class OrderService
{
    public function __construct(
        private PDO $pdo,
        private ProductRepository $products,
        private InventoryRepository $inventory,
        private AuditService $audit
    ) {
    }

    public function placeOrder(int $customerId, ?int $userId, array $cartItems): int
    {
        if ($cartItems === []) {
            throw new RuntimeException('Koszyk jest pusty.');
        }

        $this->pdo->beginTransaction();
        try {
            $totalNet = 0.0;
            $lines = [];

            foreach ($cartItems as $item) {
                $productId = (int) $item['product_id'];
                $qty = (float) $item['quantity'];
                $stock = $this->products->getAvailableStock($productId);
                if ($stock < $qty) {
                    throw new RuntimeException('Niewystarczający stan magazynowy dla: ' . $item['name']);
                }
                $inv = $this->inventory->findWarehouseWithStock($productId, $qty);
                if (!$inv) {
                    throw new RuntimeException('Brak magazynu z wystarczającym stanem dla: ' . $item['name']);
                }
                $lineTotal = round((float) $item['unit_price'] * $qty, 2);
                $totalNet += $lineTotal;
                $lines[] = [
                    'product_id' => $productId,
                    'warehouse_id' => (int) $inv['warehouse_id'],
                    'inventory_id' => (int) $inv['id'],
                    'quantity' => $qty,
                    'unit_price' => (float) $item['unit_price'],
                    'line_total' => $lineTotal,
                ];
            }

            $totalGross = grossFromNet($totalNet);
            $orderNumber = generateOrderNumber();

            $stmt = $this->pdo->prepare('INSERT INTO orders (order_number, customer_id, user_id, status, total_net, total_gross)
                VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$orderNumber, $customerId, $userId, 'NEW', $totalNet, $totalGross]);
            $orderId = (int) $this->pdo->lastInsertId();

            $lineStmt = $this->pdo->prepare('INSERT INTO order_lines (order_id, product_id, warehouse_id, quantity, unit_price, line_total)
                VALUES (?, ?, ?, ?, ?, ?)');
            $resStmt = $this->pdo->prepare('INSERT INTO reservations (order_id, product_id, warehouse_id, quantity, status)
                VALUES (?, ?, ?, ?, ?)');

            foreach ($lines as $line) {
                $lineStmt->execute([
                    $orderId,
                    $line['product_id'],
                    $line['warehouse_id'],
                    $line['quantity'],
                    $line['unit_price'],
                    $line['line_total'],
                ]);
                $resStmt->execute([
                    $orderId,
                    $line['product_id'],
                    $line['warehouse_id'],
                    $line['quantity'],
                    'ACTIVE',
                ]);
                $this->inventory->reserve($line['inventory_id'], $line['quantity']);
            }

            $this->audit->log('orders', $orderId, 'created', [
                'order_number' => $orderNumber,
                'total_gross' => $totalGross,
            ]);

            $this->pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
