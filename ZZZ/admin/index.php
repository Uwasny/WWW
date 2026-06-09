<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$orderRepo = new OrderRepository($pdo);
$invoiceRepo = new InvoiceRepository($pdo);
$inventoryRepo = new InventoryRepository($pdo);

$earningsTodayGross = $orderRepo->earningsTodayTotal();
$earningsTodayNet = $orderRepo->earningsTodayNet();
$ordersToday = $orderRepo->countToday();
$earningsByHour = $orderRepo->earningsTodayByHour();
$chartLabels = [];
$chartData = [];
for ($h = 0; $h < 24; $h++) {
    $chartLabels[] = sprintf('%02d:00', $h);
    $chartData[] = $earningsByHour[$h];
}

adminHeader('Dashboard', 'dashboard');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Dashboard</h1>

<div class="stat-grid">
    <div class="stat-card">
        <span class="font-label-sm">Zarobki dziś (brutto)</span>
        <h3><?= e(formatMoney($earningsTodayGross)) ?></h3>
        <span class="font-label-sm" style="color:var(--on-surface-variant);">Netto: <?= e(formatMoney($earningsTodayNet)) ?> · <?= $ordersToday ?> zamów.</span>
    </div>
    <div class="stat-card">
        <span class="font-label-sm">Wszystkie zamówienia</span>
        <h3><?= $orderRepo->countAll() ?></h3>
    </div>
    <div class="stat-card">
        <span class="font-label-sm">Nowe zamówienia</span>
        <h3><?= $orderRepo->countByStatus('NEW') + $orderRepo->countByStatus('CREATED') ?></h3>
    </div>
    <div class="stat-card">
        <span class="font-label-sm">Nieopłacone faktury</span>
        <h3><?= $invoiceRepo->countUnpaid() ?></h3>
    </div>
    <div class="stat-card">
        <span class="font-label-sm">Niski stan magazynowy</span>
        <h3><?= count($inventoryRepo->lowStock()) ?></h3>
    </div>
</div>

<section style="background:white;border:1px solid var(--outline-variant);border-radius:12px;padding:24px;margin-bottom:32px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:20px;">
        <div>
            <h2 class="font-headline-md" style="margin-bottom:4px;">Zarobki na dziś</h2>
            <p class="font-body-md" style="color:var(--on-surface-variant);"><?= e(date('d.m.Y')) ?> — przychód brutto wg godziny złożenia zamówienia</p>
        </div>
        <div style="text-align:right;">
            <p class="font-label-sm" style="color:var(--on-surface-variant);">Suma dnia</p>
            <p class="font-headline-lg" style="color:var(--primary);margin:0;"><?= e(formatMoney($earningsTodayGross)) ?></p>
        </div>
    </div>
    <div style="position:relative;height:320px;">
        <canvas id="earningsTodayChart"></canvas>
    </div>
</section>

<h2 class="font-headline-md" style="margin-bottom:16px;">Produkty z niskim stanem</h2>
<table class="data-table">
    <thead><tr><th>Produkt</th><th>Magazyn</th><th>Stan</th><th>Min.</th></tr></thead>
    <tbody>
        <?php foreach ($inventoryRepo->lowStock() as $row): ?>
        <tr>
            <td><?= e($row['product_name']) ?></td>
            <td><?= e($row['warehouse_name']) ?></td>
            <td><?= e((string) $row['quantity_on_hand']) ?></td>
            <td><?= e((string) $row['min_stock']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('earningsTodayChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Przychód brutto (zł)',
                data: <?= json_encode($chartData) ?>,
                backgroundColor: 'rgba(168, 57, 0, 0.75)',
                borderColor: '#a83900',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.parsed.y.toLocaleString('pl-PL', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' zł';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value.toLocaleString('pl-PL') + ' zł';
                        }
                    }
                }
            }
        }
    });
})();
</script>

<?php adminFooter(); ?>
