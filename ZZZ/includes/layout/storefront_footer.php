<nav class="mobile-nav">
    <a href="<?= e(appUrl('/public/index.php')) ?>" class="mobile-nav-item <?= ($activeNav ?? '') === 'home' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">home</span>
        <span>Strona główna</span>
    </a>
    <a href="<?= e(appUrl('/public/category.php')) ?>" class="mobile-nav-item <?= ($activeNav ?? '') === 'categories' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">grid_view</span>
        <span>Kategorie</span>
    </a>
    <a href="<?= e(appUrl('/public/search.php')) ?>" class="mobile-nav-item">
        <span class="material-symbols-outlined">search</span>
        <span>Szukaj</span>
    </a>
    <a href="<?= e(appUrl('/public/cart.php')) ?>" class="mobile-nav-item <?= ($activeNav ?? '') === 'cart' ? 'cart-active active' : '' ?>">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span>Koszyk</span>
    </a>
    <a href="<?= e(isLoggedIn() && userRole() === 'customer' ? appUrl('/panel/') : appUrl('/public/login.php')) ?>" class="mobile-nav-item">
        <span class="material-symbols-outlined">person</span>
        <span>Profil</span>
    </a>
</nav>

<footer>
    <div class="footer-container-wrap">
        <div style="text-align: center;">
            <h3 class="footer-brand">MarketFlow</h3>
        </div>
        <div class="footer-links">
            <a href="<?= e(appUrl('/public/about.php')) ?>">O nas</a>
            <a href="<?= e(appUrl('/public/help.php')) ?>">Centrum pomocy</a>
            <a href="<?= e(appUrl('/public/privacy.php')) ?>">Polityka prywatności</a>
            <a href="<?= e(appUrl('/public/terms.php')) ?>">Warunki serwisu</a>
            <a href="<?= e(appUrl('/public/contact.php')) ?>">Kontakt</a>
        </div>
        <div class="footer-copyright-text">
            © <?= date('Y') ?> MarketFlow. Wszystkie prawa zastrzeżone.
        </div>
    </div>
</footer>

</body>
</html>
