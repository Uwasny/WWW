<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (isLoggedIn()) {
    redirectAfterLogin();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        redirectAfterLogin();
    }
    $error = 'Nieprawidłowy e-mail lub hasło.';
}

$pageTitle = 'Logowanie | MarketFlow';
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
                <span class="material-symbols-outlined">lock</span>
            </div>
            <h1>MarketFlow</h1>
            <p class="font-body-md">Zaloguj się do swojego konta</p>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="alert-success"><?= e($msg) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-msg"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="auth-form" method="post">
            <?= csrfField() ?>
            <div>
                <label for="email">E-mail lub nazwa użytkownika</label>
                <input type="text" id="email" name="email" required autofocus>
            </div>
            <div>
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="checkout-btn">Zaloguj się</button>
        </form>
        <p class="hint">Admin: admin@marketflow.pl / admin123<br>Klient: example@example.com / client123</p>
        <p class="hint">Nie masz konta? <a href="<?= e(appUrl('/public/register.php')) ?>">Zarejestruj się</a></p>
        <p class="hint"><a href="<?= e(appUrl('/public/index.php')) ?>">← Powrót do sklepu</a></p>
    </div>
</div>
</body>
</html>
