<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Lütfen tüm alanları doldur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi yaz.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalı.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Bu e-posta zaten kayıtlı.';
        } else {
            $role = firstUserWillBeAdmin($pdo) ? 'admin' : 'user';
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            $success = $role === 'admin'
                ? 'Kayıt başarılı. İlk kullanıcı olduğun için admin yetkisi verildi.'
                : 'Kayıt başarılı. Şimdi giriş yapabilirsin.';
        }
    }
}

$pageTitle = 'Kayıt | ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="form-page">
    <div class="form-box">
        <h1>Kayıt Ol</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Ad Soyad</label>
                <input id="name" type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">E-posta</label>
                <input id="email" type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Şifre</label>
                <input id="password" type="password" name="password" minlength="6" required>
            </div>

            <button class="btn" type="submit">Kayıt Ol</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
