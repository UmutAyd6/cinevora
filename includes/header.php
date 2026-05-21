<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
    <header class="site-header">
        <div class="container navbar">
            <a href="<?= url('index.php') ?>" class="logo" aria-label="Cinevora ana sayfa">
                <img src="<?= asset('assets/images/cinevora-logo.svg') ?>" alt="Cinevora">
            </a>

            <nav class="nav-menu" aria-label="Ana menü">
                <a href="<?= url('index.php') ?>">Ana Sayfa</a>

                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('account.php') ?>">Hesabım</a>

                    <?php if (isAdmin()): ?>
                        <a href="<?= url('admin/dashboard.php') ?>">Admin</a>
                    <?php endif; ?>

                    <a href="<?= url('logout.php') ?>">Çıkış</a>
                <?php else: ?>
                    <a href="<?= url('login.php') ?>">Giriş</a>
                    <a href="<?= url('register.php') ?>" class="nav-btn">Kayıt Ol</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="site-main">
