<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user !== null && $user['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();

    if (!isAdmin()) {
        redirect('/index.php');
    }
}
