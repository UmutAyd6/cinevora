<?php
function uploadPoster(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Kapak görseli yüklenemedi.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $mimeType = mime_content_type($file['tmp_name']);
    if (!isset($allowedTypes[$mimeType])) {
        throw new RuntimeException('Kapak görseli JPG, PNG veya WEBP olmalı.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }

    $fileName = uniqid('movie_', true) . '.' . $allowedTypes[$mimeType];
    $target = UPLOAD_DIR . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Kapak görseli kaydedilemedi.');
    }

    return UPLOAD_PATH . '/' . $fileName;
}

function selectedCategoryIds(PDO $pdo, int $movieId): array
{
    $stmt = $pdo->prepare('SELECT category_id FROM movie_categories WHERE movie_id = ?');
    $stmt->execute([$movieId]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function syncMovieCategories(PDO $pdo, int $movieId, array $categoryIds): void
{
    $pdo->prepare('DELETE FROM movie_categories WHERE movie_id = ?')->execute([$movieId]);

    $stmt = $pdo->prepare('INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)');
    foreach ($categoryIds as $categoryId) {
        $stmt->execute([$movieId, (int)$categoryId]);
    }
}
