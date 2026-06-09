<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'Kontakt | MarketFlow';
require dirname(__DIR__) . '/includes/layout/storefront_header.php';
?>

<main class="page-container">
    <h1 class="font-headline-lg" style="margin-bottom:24px;">Kontakt</h1>
    
    <div class="contact-grid">
        <section class="info-page-section">
            <h2 class="font-headline-md info-page-subheading">Dane firmy</h2>
            <p class="font-body-md info-page-text">
                <strong>MarketFlow Sp. z o.o.</strong><br>
                ul. Technologiczna 10<br>
                00-001 Warszawa<br>
                NIP: 123-456-78-90
            </p>
        </section>

        <section class="info-page-section">
            <h2 class="font-headline-md info-page-subheading">Biuro Obsługi Klienta</h2>
            <p class="font-body-md info-page-text">
                E-mail: kontakt@marketflow.pl<br>
                Tel: +48 123 456 789<br>
                Godziny pracy: Pon - Pt, 8:00 - 16:00
            </p>
        </section>
        <section class="info-page-section">
            <h2 class="font-headline-md info-page-subheading">Reklamacje</h2>
            <p class="font-body-md info-page-text">
                Zgłoszenia na temat reklamacji przesyłać w nieparzyste dni parzystego miesiąca gdy słońce w zenicie (czyt. w godzinach 12:00-12:15)
            </p>
        </section>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/layout/storefront_footer.php'; ?>