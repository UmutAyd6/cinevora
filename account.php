<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$stmt = $pdo->prepare("
    SELECT m.*, wm.created_at AS watched_at
    FROM watched_movies wm
    INNER JOIN movies m ON m.id = wm.movie_id
    WHERE wm.user_id = ?
    ORDER BY wm.created_at DESC
");
$stmt->execute([currentUser()['id']]);
$movies = $stmt->fetchAll();

$pageTitle = 'Hesabım | ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="page-title">
    <div class="container">
        <h1>Hesabım</h1>
        <p>İzledim olarak işaretlediğin filmler burada görünür.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($movies): ?>
            <div class="movies-grid">
                <?php foreach ($movies as $movie): ?>
                    <a class="movie-card" href="<?= url('movie.php?slug=' . urlencode($movie['slug'])) ?>">
                        <img src="<?= h(posterUrl($movie['poster_path'])) ?>" alt="<?= h($movie['title']) ?>">
                        <div class="movie-card-body">
                            <h3><?= h($movie['title']) ?></h3>
                            <p><?= h($movie['short_description']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-box">Henüz film işaretlemedin.</div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
