<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'Centrum pomocy | MarketFlow';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main class="page-container">
    <h1 class="font-headline-lg" style="margin-bottom:24px;">Centrum pomocy</h1>
    
    <div class="info-page-grid">
        <section class="info-page-section">
            <h2 class="font-headline-md info-page-subheading">Jak założyć konto?</h2>
            <p class="font-body-md info-page-text">Aby założyć konto, przejdź do sekcji "Zarejestruj się" i wypełnij formularz. Po weryfikacji przez administratora otrzymasz dostęp do pełnej oferty B2B.</p>
        </section>

        <section class="info-page-section">
            <h2 class="font-headline-md info-page-subheading">Metody płatności</h2>
            <p class="font-body-md info-page-text">Obsługujemy płatności przelewem tradycyjnym, szybkie płatności online oraz zakupy z odroczonym terminem płatności dla stałych partnerów.</p>
        </section>

        <section class="info-page-section">
            <h2 class="font-headline-md info-page-subheading">Dostawa</h2>
            <p class="font-body-md info-page-text">Większość zamówień realizujemy w ciągu 24-48 godzin roboczych. Współpracujemy z wiodącymi firmami kurierskimi oraz oferujemy transport własny.</p>
        </section>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>