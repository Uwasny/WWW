<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'Warunki serwisu | MarketFlow';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main class="page-container">
    <h1 class="font-headline-lg" style="margin-bottom:24px;">Warunki serwisu</h1>
    <section class="info-page-content">
        <h2 class="font-headline-md info-page-subheading">1. Postanowienia wstępne</h2>
        <p class="info-page-text">Niniejszy regulamin określa zasady korzystania z platformy MarketFlow. Korzystanie z serwisu oznacza akceptację warunków.</p>

        <h2 class="font-headline-md info-page-subheading">2. Zamówienia</h2>
        <p class="info-page-text">Zamówienia mogą składać wyłącznie podmioty gospodarcze posiadające aktywne konto. Ceny podane w systemie po zalogowaniu są wiążące.</p>

        <h2 class="font-headline-md info-page-subheading">3. Reklamacje</h2>
        <p class="info-page-text">Reklamacje dotyczące braków ilościowych lub wad jakościowych należy zgłaszać w terminie 7 dni od otrzymania towaru.</p>
    </section>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>