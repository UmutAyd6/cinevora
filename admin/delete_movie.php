<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$movieId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    redirect('/admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete = $pdo->prepare('DELETE FROM movies WHERE id = ?');
    $delete->execute([$movieId]);
    redirect('/admin/dashboard.php');
}

$pageTitle = 'Film Sil | ' . APP_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="form-page">
    <div class="form-box">
        <h1>Film Sil</h1>
        <p class="muted-text"><?= h($movie['title']) ?> kalıcı olarak silinsin mi?</p>

        <form method="POST" class="actions-row">
            <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
            <button class="btn btn-danger" type="submit">Evet, Sil</button>
            <a class="btn btn-secondary" href="<?= url('admin/dashboard.php') ?>">Vazgeç</a>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
