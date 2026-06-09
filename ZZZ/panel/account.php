<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('customer');
require_once dirname(__DIR__) . '/includes/layout/panel_layout.php';

$userRepo = new UserRepository($pdo);
$customerId = customerId();
$customerRepo = new CustomerRepository($pdo);

// Obsługa zapisu danych firmy, jeśli użytkownik ich jeszcze nie ma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$customerId) {
    verifyCsrf($_POST['csrf_token'] ?? null);

    $name = trim($_POST['company_name'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['contact_email'] ?? '');

    if ($name !== '' && $nip !== '') {
        // Tworzymy wpis w tabeli customers
        $stmt = $pdo->prepare('INSERT INTO customers (company_name, vat_number, address, contact_email, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$name, $nip, $address, $email]);
        $newCustomerId = (int) $pdo->lastInsertId();

        // Łączymy użytkownika z nową firmą
        $userRepo->setCustomerId((int)$_SESSION['user_id'], $newCustomerId);

        // Aktualizujemy dane w sesji, aby zmiany były widoczne natychmiast
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['customer_id'] = $newCustomerId;
        }

        flash('success', 'Dane firmy zostały zapisane pomyślnie.');
        redirect(appUrl('/panel/account.php'));
    }
}

// Pobieramy dane tylko jeśli customerId nie jest null, aby uniknąć TypeError
$customer = $customerId ? $customerRepo->findById((int)$customerId) : null;

panelHeader('Dane firmy', 'account');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Dane firmy</h1>

<?php if ($customer): ?>
    <div class="order-card-panel customer-details-card">
        <p><strong>Nazwa firmy:</strong> <?= e($customer['company_name']) ?></p>
        <p><strong>NIP:</strong> <?= e($customer['vat_number'] ?? '—') ?></p>
        <p><strong>Adres:</strong> <?= e($customer['address'] ?? '—') ?></p>
        <p><strong>E-mail kontaktowy:</strong> <?= e($customer['contact_email'] ?? '—') ?></p>
        <p><strong>Warunki płatności:</strong> <?= e($customer['billing_terms'] ?? 'Standardowe') ?></p>
        <p><strong>Cennik:</strong> <?= e($customer['price_list_name'] ?? 'Domyślny') ?></p>
        <p><strong>Konto od:</strong> <?= e(date('d.m.Y', strtotime($customer['created_at']))) ?></p>
    </div>
<?php else: ?>
    <div class="auth-card customer-form-card">
        <p class="font-body-md customer-form-intro-text">
            Twoje konto nie jest jeszcze powiązane z firmą. Uzupełnij poniższe dane, aby móc korzystać z pełnej oferty B2B.
        </p>

        <form method="POST" class="auth-form">
            <?= csrfField() ?>
            <div>
                <label for="company_name">Pełna nazwa firmy</label>
                <input type="text" id="company_name" name="company_name" required>
            </div>
            <div>
                <label for="nip">NIP</label>
                <input type="text" id="nip" name="nip" required>
            </div>
            <div>
                <label for="address">Adres siedziby</label>
                <input type="text" id="address" name="address" placeholder="ul. Przykładowa 1, 00-000 Miasto">
            </div>
            <div>
                <label for="contact_email">Firmowy e-mail kontaktowy</label>
                <input type="email" id="contact_email" name="contact_email">
            </div> 
            <button type="submit" class="checkout-btn customer-form-submit-btn">Zapisz dane firmy</button>
        </form>
    </div>
<?php endif; ?>

<?php panelFooter(); ?>
