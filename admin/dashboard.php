<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$movies = $pdo->query("
    SELECT m.*, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS categories
    FROM movies m
    LEFT JOIN movie_categories mc ON mc.movie_id = m.id
    LEFT JOIN categories c ON c.id = mc.category_id
    GROUP BY m.id
    ORDER BY m.created_at DESC
")->fetchAll();

$pageTitle = 'Admin Paneli | ' . APP_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="page-title">
    <div class="container admin-title">
        <div>
            <h1>Film Yönetimi</h1>
            <p>Filmleri ekle, düzenle veya yayından kaldır.</p>
        </div>
        <a class="btn" href="<?= url('admin/add_movie.php') ?>">Film Ekle</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($movies): ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Kategori</th>
                            <th>Yıl</th>
                            <th>IMDb</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td>
                                    <div class="admin-movie-cell">
                                        <img src="<?= h(posterUrl($movie['poster_path'])) ?>" alt="<?= h($movie['title']) ?>">
                                        <strong><?= h($movie['title']) ?></strong>
                                    </div>
                                </td>
                                <td><?= h($movie['categories'] ?: 'Kategori yok') ?></td>
                                <td><?= h((string)($movie['release_year'] ?: '-')) ?></td>
                                <td><?= h((string)($movie['imdb_score'] ?: '-')) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a class="btn btn-secondary" href="<?= url('admin/edit_movie.php?id=' . $movie['id']) ?>">Düzenle</a>
                                        <a class="btn btn-danger" href="<?= url('admin/delete_movie.php?id=' . $movie['id']) ?>">Sil</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-box">Henüz film eklenmedi.</div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
