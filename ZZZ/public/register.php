<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

// Jeśli użytkownik jest już zalogowany, przekieruj na stronę główną
if (isLoggedIn()) {
    redirect(appUrl('/public/index.php'));
}

$userRepo = new UserRepository($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Podstawowa walidacja
    if (empty($name)) $errors[] = "Imię i nazwisko są wymagane.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Niepoprawny format adresu e-mail.";
    if (strlen($password) < 6) $errors[] = "Hasło musi mieć co najmniej 6 znaków.";
    if ($password !== $confirmPassword) $errors[] = "Hasła nie są identyczne.";

    if (empty($errors)) {
        // Sprawdzenie czy użytkownik już istnieje
        if ($userRepo->findByEmail($email)) {
            $errors[] = "Użytkownik o tym adresie e-mail już istnieje.";
        } else {
            $userId = $userRepo->create([
                'username' => $name, // UserRepository oczekuje klucza 'username'
                'password' => $password, // UserRepository sam hashuje hasło
                'email' => $email,
                'role_id' => 2, // Zakładamy ID 2 dla roli 'customer'
                'customer_id' => null,
                'is_active' => 1
            ]);

            if ($userId) {
                flash('success', 'Konto zostało utworzone. Możesz się teraz zalogować.');
                redirect(appUrl('/public/login.php'));
            } else {
                $errors[] = "Wystąpił błąd podczas tworzenia konta.";
            }
        }
    }
}

$pageTitle = 'Zarejestruj się | MarketFlow';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-box">
                <span class="material-symbols-outlined">person_add</span>
            </div>
            <h1 class="font-headline-md">Utwórz konto</h1>
            <p class="font-body-md">Dołącz do MarketFlow i zacznij zakupy</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-msg">
                <?php foreach ($errors as $error): ?>
                    <p class="font-label-md"><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrfField() ?>
            <div>
                <label>Nazwa</label>
                <input type="text" name="name" value="<?= e($name ?? '') ?>" required>
            </div>
            <div>
                <label>Adres e-mail</label>
                <input type="email" name="email" value="<?= e($email ?? '') ?>" required>
            </div>
            <div>
                <label>Hasło</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Powtórz hasło</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="checkout-btn">
                Zarejestruj się
            </button>
        </form>
        <div class="auth-footer">
            <p class="font-body-md">Masz już konto? <a href="<?= e(appUrl('/public/login.php')) ?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">Zaloguj się</a></p>
            <p class="hint" style="margin-top: 16px;"><a href="<?= e(appUrl('/public/index.php')) ?>">← Powrót do sklepu</a></p>
        </div>
    </div>
</div>
</body>
</html>