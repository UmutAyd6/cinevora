<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$search = trim($_GET['q'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

$sql = "
    SELECT DISTINCT m.*
    FROM movies m
    LEFT JOIN movie_categories mc ON mc.movie_id = m.id
    WHERE 1 = 1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (m.title LIKE ? OR m.director LIKE ? OR m.actors LIKE ?)";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
}

if ($categoryId > 0) {
    $sql .= " AND mc.category_id = ?";
    $params[] = $categoryId;
}

$sql .= ' ORDER BY m.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();
$movieCount = count($movies);

$recommendations = array_map(function (array $movie): array {
    return [
        'title' => $movie['title'],
        'description' => $movie['short_description'] ?: 'Bu film için kısa açıklama eklenmedi.',
        'year' => $movie['release_year'],
        'imdb' => $movie['imdb_score'],
        'poster' => posterUrl($movie['poster_path']),
        'url' => url('movie.php?slug=' . urlencode($movie['slug'])),
    ];
}, $movies);
$initialRecommendation = $recommendations
    ? $recommendations[array_rand($recommendations)]
    : null;

$pageTitle = APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-layout">
        <?php if ($initialRecommendation): ?>
            <a class="random-pick" href="<?= h($initialRecommendation['url']) ?>" data-random-pick>
                <span class="random-pick-kicker">Sana önerim</span>
                <div class="random-pick-poster">
                    <img src="<?= h($initialRecommendation['poster']) ?>" alt="<?= h($initialRecommendation['title']) ?>" data-random-poster>
                </div>
                <div class="random-pick-body">
                    <h2 data-random-title><?= h($initialRecommendation['title']) ?></h2>
                    <p data-random-description><?= h($initialRecommendation['description']) ?></p>
                    <span class="rating-pill" data-random-meta>
                        <?= h((string)$initialRecommendation['year']) ?>
                        <?php if ($initialRecommendation['imdb']): ?>
                            · <span class="rating-star">★</span> IMDb <?= h((string)$initialRecommendation['imdb']) ?>
                        <?php endif; ?>
                    </span>
                </div>
            </a>
        <?php endif; ?>

        <div class="hero-copy">
            <span class="eyebrow">Kişisel film arşivin</span>
            <h1>İzlemek istediğin filmi şık bir vitrinde keşfet.</h1>
            <p>Türlere göre filtrele, detayları incele, izlediklerini hesabında tut ve sıradaki filmi daha hızlı seç.</p>

            <div class="hero-stats" aria-label="Film istatistikleri">
                <div>
                    <strong><?= $movieCount ?></strong>
                    <span>Film</span>
                </div>
                <div>
                    <strong><?= count($categories) ?></strong>
                    <span>Kategori</span>
                </div>
                <div>
                    <strong><span class="rating-star">★</span> IMDb</strong>
                    <span>Puan desteği</span>
                </div>
            </div>
        </div>

        <form class="search-panel" method="GET" action="<?= url('index.php') ?>">
            <div class="panel-title">
                <span>Hızlı arama</span>
                <strong>Bugün ne izliyorsun?</strong>
            </div>

            <div class="form-group">
                <label for="q">Film, yönetmen veya oyuncu</label>
                <input id="q" type="search" name="q" value="<?= h($search) ?>" placeholder="Örn. Interstellar">
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <select id="category" name="category">
                    <option value="0">Tüm kategoriler</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $categoryId === (int)$category['id'] ? 'selected' : '' ?>>
                            <?= h($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn" type="submit">Filmleri Göster</button>
        </form>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="section-kicker">Arşiv</span>
                <h2>Öne çıkan filmler</h2>
                <p><?= $movieCount ?> film listeleniyor.</p>
            </div>
        </div>

        <?php if ($movies): ?>
            <div class="movies-grid">
                <?php foreach ($movies as $movie): ?>
                    <a class="movie-card" href="<?= url('movie.php?slug=' . urlencode($movie['slug'])) ?>">
                        <img src="<?= h(posterUrl($movie['poster_path'])) ?>" alt="<?= h($movie['title']) ?>">
                        <div class="movie-card-body">
                            <h3><?= h($movie['title']) ?></h3>
                            <p><?= h($movie['short_description']) ?></p>
                            <span class="rating-pill">
                                <?= h((string)$movie['release_year']) ?>
                                <?php if ($movie['imdb_score']): ?>
                                    · <span class="rating-star">★</span> IMDb <?= h((string)$movie['imdb_score']) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-box">Aradığın kritere uygun film bulunamadı.</div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
