<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requireRole('admin');
require_once dirname(__DIR__) . '/includes/layout/admin_layout.php';

$userRepo = new UserRepository($pdo);
$adminRepo = new AdminRepository($pdo);
$customerRepo = new CustomerRepository($pdo);
$audit = new AuditService(new AuditRepository($pdo));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
    $roleId = (int) $_POST['role_id'];
    $customerId = $roleId === 2 ? ((int) ($_POST['customer_id'] ?? 0) ?: null) : null;

    $id = $userRepo->create([
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role_id' => $roleId,
        'customer_id' => $customerId,
        'is_active' => 1,
    ]);
    $audit->log('users', $id, 'created', ['email' => $_POST['email']]);
    flash('success', 'Użytkownik utworzony.');
    redirect(appUrl('/admin/users.php'));
}

$users = $userRepo->all();
$roles = $adminRepo->roles();
$customers = $customerRepo->all();

adminHeader('Użytkownicy', 'users');
?>

<h1 class="font-headline-lg" style="margin-bottom:24px;">Użytkownicy</h1>

<form method="post" class="form-grid" style="background:white;padding:24px;border-radius:12px;margin-bottom:24px;">
    <?= csrfField() ?>
    <input type="text" name="username" placeholder="Nazwa użytkownika" required>
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="password" placeholder="Hasło" required>
    <select name="role_id" id="role_id" required>
        <?php foreach ($roles as $r): ?>
        <option value="<?= (int) $r['id'] ?>"><?= e($r['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="customer_id">
        <option value="">— Klient B2B (dla roli customer) —</option>
        <?php foreach ($customers as $c): ?>
        <option value="<?= (int) $c['id'] ?>"><?= e($c['company_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-primary">Dodaj użytkownika</button>
</form>

<table class="data-table">
    <thead><tr><th>ID</th><th>Login</th><th>E-mail</th><th>Rola</th><th>Klient</th><th>Aktywny</th></tr></thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= e($u['username']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role_name']) ?></td>
            <td><?= e($u['company_name'] ?? '—') ?></td>
            <td><?= (int) $u['is_active'] ? 'Tak' : 'Nie' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php adminFooter(); ?>
