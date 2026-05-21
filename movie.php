<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$slug = trim($_GET['slug'] ?? '');
$id = (int)($_GET['id'] ?? 0);

if ($slug !== '') {
    $stmt = $pdo->prepare('SELECT * FROM movies WHERE slug = ?');
    $stmt->execute([$slug]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
    $stmt->execute([$id]);
}

$movie = $stmt->fetch();

if (!$movie) {
    redirect('/index.php');
}

$categoryStmt = $pdo->prepare("
    SELECT c.name
    FROM categories c
    INNER JOIN movie_categories mc ON mc.category_id = c.id
    WHERE mc.movie_id = ?
    ORDER BY c.name
");
$categoryStmt->execute([$movie['id']]);
$movieCategories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

$similarStmt = $pdo->prepare("
    SELECT m.*, matches.shared_categories
    FROM movies m
    INNER JOIN (
        SELECT candidate_mc.movie_id, COUNT(*) AS shared_categories
        FROM movie_categories current_mc
        INNER JOIN movie_categories candidate_mc ON candidate_mc.category_id = current_mc.category_id
        WHERE current_mc.movie_id = ? AND candidate_mc.movie_id != ?
        GROUP BY candidate_mc.movie_id
    ) matches ON matches.movie_id = m.id
    ORDER BY matches.shared_categories DESC, m.imdb_score DESC, m.created_at DESC
    LIMIT 4
");
$similarStmt->execute([$movie['id'], $movie['id']]);
$similarMovies = $similarStmt->fetchAll();

$isWatched = false;
if (isLoggedIn()) {
    $watchedStmt = $pdo->prepare('SELECT 1 FROM watched_movies WHERE user_id = ? AND movie_id = ?');
    $watchedStmt->execute([currentUser()['id'], $movie['id']]);
    $isWatched = (bool)$watchedStmt->fetchColumn();
}

$pageTitle = $movie['title'] . ' | ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="movie-hero">
    <div class="container movie-hero-layout">
        <div class="movie-poster">
            <img src="<?= h(posterUrl($movie['poster_path'])) ?>" alt="<?= h($movie['title']) ?>">
        </div>

        <div class="movie-detail">
            <div class="movie-tags">
                <?php foreach ($movieCategories as $category): ?>
                    <span><?= h($category) ?></span>
                <?php endforeach; ?>
            </div>

            <h1><?= h($movie['title']) ?></h1>
            <p><?= h($movie['short_description']) ?></p>

            <div class="meta-grid">
                <div><span>Yıl</span><strong><?= h((string)($movie['release_year'] ?: 'Belirtilmedi')) ?></strong></div>
                <div><span>Süre</span><strong><?= $movie['duration_minutes'] ? h((string)$movie['duration_minutes']) . ' dk' : 'Belirtilmedi' ?></strong></div>
                <div>
                    <span>IMDb</span>
                    <strong class="rating-detail"><?= $movie['imdb_score'] ? '<span class="rating-star">★</span> ' . h((string)$movie['imdb_score']) : 'Yok' ?></strong>
                </div>
            </div>

            <div class="detail-text">
                <h2>Film Hakkında</h2>
                <p><?= nl2br(h($movie['description'] ?: 'Bu film için henüz açıklama eklenmedi.')) ?></p>
            </div>

            <div class="detail-list">
                <p><strong>Yönetmen:</strong> <?= h($movie['director'] ?: 'Belirtilmedi') ?></p>
                <p><strong>Oyuncular:</strong> <?= h($movie['actors'] ?: 'Belirtilmedi') ?></p>
            </div>

            <div class="actions-row">
                <?php if (isLoggedIn()): ?>
                    <form method="POST" action="<?= url('toggle_watched.php') ?>">
                        <input type="hidden" name="movie_id" value="<?= (int)$movie['id'] ?>">
                        <button class="btn" type="submit"><?= $isWatched ? 'İzlendi İşaretini Kaldır' : 'İzledim' ?></button>
                    </form>
                <?php else: ?>
                    <a class="btn" href="<?= url('login.php') ?>">İzledim demek için giriş yap</a>
                <?php endif; ?>

                <?php if ($movie['trailer_url']): ?>
                    <a class="btn btn-secondary" href="<?= h($movie['trailer_url']) ?>" target="_blank" rel="noopener">Fragmanı Aç</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($similarMovies): ?>
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Benzer Filmler</h2>
                    <p>Bu filmle ortak türe sahip öneriler.</p>
                </div>
            </div>

            <div class="movies-grid">
                <?php foreach ($similarMovies as $similarMovie): ?>
                    <a class="movie-card" href="<?= url('movie.php?slug=' . urlencode($similarMovie['slug'])) ?>">
                        <img src="<?= h(posterUrl($similarMovie['poster_path'])) ?>" alt="<?= h($similarMovie['title']) ?>">
                        <div class="movie-card-body">
                            <h3><?= h($similarMovie['title']) ?></h3>
                            <p><?= h($similarMovie['short_description']) ?></p>
                            <span><?= (int)$similarMovie['shared_categories'] ?> ortak tür</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
