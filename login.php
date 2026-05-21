<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        redirect('/index.php');
    }

    $error = 'E-posta veya şifre hatalı.';
}

$pageTitle = 'Giriş | ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="form-page">
    <div class="form-box">
        <h1>Giriş Yap</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">E-posta</label>
                <input id="email" type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Şifre</label>
                <input id="password" type="password" name="password" required>
            </div>

            <button class="btn" type="submit">Giriş Yap</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
