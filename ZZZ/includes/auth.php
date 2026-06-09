<?php

declare(strict_types=1);

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user !== null) {
        return $user;
    }

    $repo = new UserRepository(db());
    $user = $repo->findById((int) $_SESSION['user_id']);
    return $user ?: null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function userRole(): ?string
{
    $user = currentUser();
    return $user['role_name'] ?? null;
}

function login(string $login, string $password): bool
{
    $repo = new UserRepository(db());
    $user = $repo->findByLogin($login);

    if (!$user || !(int) $user['is_active']) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['role'] = $user['role_name'];
    $_SESSION['customer_id'] = $user['customer_id'] !== null ? (int) $user['customer_id'] : null;

    return true;
}

function logout(): void
{
    unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['customer_id']);
}

function requireLogin(string $redirectTo = '/public/login.php'): void
{
    if (!isLoggedIn()) {
        $base = rtrim((string) appConfig('APP_URL', ''), '/');
        redirect($base . $redirectTo);
    }
}

function requireRole(string $role): void
{
    requireLogin();
    if (userRole() !== $role) {
        http_response_code(403);
        exit('Brak uprawnień.');
    }
}

function redirectAfterLogin(): never
{
    $base = rtrim((string) appConfig('APP_URL', ''), '/');
    $role = userRole();

    if ($role === 'admin') {
        redirect($base . '/admin/');
    }

    if ($role === 'customer') {
        redirect($base . '/panel/');
    }

    redirect($base . '/public/');
}

function customerId(): ?int
{
    $user = currentUser();
    if (!$user || $user['customer_id'] === null) {
        return null;
    }
    return (int) $user['customer_id'];
}

function priceListIdForUser(): int
{
    $cid = customerId();
    if ($cid) {
        $repo = new CustomerRepository(db());
        $customer = $repo->findById($cid);
        if ($customer && $customer['price_list_id']) {
            return (int) $customer['price_list_id'];
        }
    }
    return 1;
}
