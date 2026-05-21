<?php
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url($path);
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function posterUrl(?string $posterPath): string
{
    if ($posterPath) {
        return asset($posterPath);
    }

    return 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=80';
}

function makeSlug(string $title): string
{
    $map = [
        'ç' => 'c', 'Ç' => 'c',
        'ğ' => 'g', 'Ğ' => 'g',
        'ı' => 'i', 'I' => 'i', 'İ' => 'i',
        'ö' => 'o', 'Ö' => 'o',
        'ş' => 's', 'Ş' => 's',
        'ü' => 'u', 'Ü' => 'u',
    ];

    $slug = strtr($title, $map);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-') ?: 'film';
}

function uniqueSlug(PDO $pdo, string $title, ?int $ignoreId = null): string
{
    $base = makeSlug($title);
    $slug = $base;
    $counter = 2;

    while (true) {
        $sql = 'SELECT id FROM movies WHERE slug = ?';
        $params = [$slug];

        if ($ignoreId) {
            $sql .= ' AND id != ?';
            $params[] = $ignoreId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $base . '-' . $counter;
        $counter++;
    }
}

function firstUserWillBeAdmin(PDO $pdo): bool
{
    return (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0;
}
