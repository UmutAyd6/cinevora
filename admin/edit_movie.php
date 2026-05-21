<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/upload_helpers.php';

requireAdmin();

$movieId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    redirect('/admin/dashboard.php');
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$selectedCategories = selectedCategoryIds($pdo, $movieId);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $director = trim($_POST['director'] ?? '');
    $actors = trim($_POST['actors'] ?? '');
    $releaseYear = $_POST['release_year'] !== '' ? (int)$_POST['release_year'] : null;
    $durationMinutes = $_POST['duration_minutes'] !== '' ? (int)$_POST['duration_minutes'] : null;
    $imdbScore = $_POST['imdb_score'] !== '' ? (float)$_POST['imdb_score'] : null;
    $trailerUrl = trim($_POST['trailer_url'] ?? '');
    $categoryIds = $_POST['categories'] ?? [];

    if ($title === '') {
        $error = 'Film adı zorunlu.';
    } else {
        try {
            $posterPath = uploadPoster($_FILES['poster'] ?? []) ?? $movie['poster_path'];
            $slug = uniqueSlug($pdo, $title, $movieId);

            $stmt = $pdo->prepare("
                UPDATE movies
                SET title = ?, slug = ?, short_description = ?, description = ?, director = ?, actors = ?,
                    release_year = ?, duration_minutes = ?, imdb_score = ?, poster_path = ?, trailer_url = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $title,
                $slug,
                $shortDescription ?: null,
                $description ?: null,
                $director ?: null,
                $actors ?: null,
                $releaseYear,
                $durationMinutes,
                $imdbScore,
                $posterPath,
                $trailerUrl ?: null,
                $movieId,
            ]);

            syncMovieCategories($pdo, $movieId, $categoryIds);
            redirect('/admin/dashboard.php');
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$pageTitle = 'Film Düzenle | ' . APP_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="page-title">
    <div class="container">
        <h1>Film Düzenle</h1>
        <p><?= h($movie['title']) ?> bilgilerini güncelle.</p>
    </div>
</section>

<section class="form-page">
    <form class="form-box form-box-wide" method="POST" enctype="multipart/form-data">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($movie['poster_path']): ?>
            <div class="current-poster">
                <img src="<?= h(posterUrl($movie['poster_path'])) ?>" alt="<?= h($movie['title']) ?>">
                <span>Yeni görsel seçmezsen mevcut kapak korunur.</span>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/movie_form_fields.php'; ?>

        <button class="btn" type="submit">Değişiklikleri Kaydet</button>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
