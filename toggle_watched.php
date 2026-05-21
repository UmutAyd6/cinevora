<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$movieId = (int)($_POST['movie_id'] ?? 0);

if ($movieId > 0) {
    $check = $pdo->prepare('SELECT 1 FROM watched_movies WHERE user_id = ? AND movie_id = ?');
    $check->execute([currentUser()['id'], $movieId]);

    if ($check->fetchColumn()) {
        $stmt = $pdo->prepare('DELETE FROM watched_movies WHERE user_id = ? AND movie_id = ?');
        $stmt->execute([currentUser()['id'], $movieId]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO watched_movies (user_id, movie_id) VALUES (?, ?)');
        $stmt->execute([currentUser()['id'], $movieId]);
    }
}

$back = $_SERVER['HTTP_REFERER'] ?? url('index.php');
header('Location: ' . $back);
exit;
