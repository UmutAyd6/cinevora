<?php
$movie = $movie ?? [];
$selectedCategories = $selectedCategories ?? [];
?>

<div class="form-grid">
    <div class="form-group">
        <label for="title">Film Adı</label>
        <input id="title" type="text" name="title" value="<?= h($movie['title'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="release_year">Yıl</label>
        <input id="release_year" type="number" name="release_year" min="1888" max="2100" value="<?= h((string)($movie['release_year'] ?? '')) ?>">
    </div>

    <div class="form-group">
        <label for="duration_minutes">Süre (dk)</label>
        <input id="duration_minutes" type="number" name="duration_minutes" min="1" value="<?= h((string)($movie['duration_minutes'] ?? '')) ?>">
    </div>

    <div class="form-group">
        <label for="imdb_score">IMDb</label>
        <input id="imdb_score" type="number" name="imdb_score" min="0" max="10" step="0.1" value="<?= h((string)($movie['imdb_score'] ?? '')) ?>">
    </div>
</div>

<div class="form-group">
    <label for="short_description">Kısa Açıklama</label>
    <input id="short_description" type="text" name="short_description" maxlength="255" value="<?= h($movie['short_description'] ?? '') ?>">
</div>

<div class="form-group">
    <label for="description">Film Hakkında</label>
    <textarea id="description" name="description" rows="6"><?= h($movie['description'] ?? '') ?></textarea>
</div>

<div class="form-grid">
    <div class="form-group">
        <label for="director">Yönetmen</label>
        <input id="director" type="text" name="director" value="<?= h($movie['director'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="actors">Oyuncular</label>
        <input id="actors" type="text" name="actors" value="<?= h($movie['actors'] ?? '') ?>">
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label for="poster">Kapak Görseli</label>
        <input id="poster" type="file" name="poster" accept="image/jpeg,image/png,image/webp">
    </div>

    <div class="form-group">
        <label for="trailer_url">Fragman Linki</label>
        <input id="trailer_url" type="url" name="trailer_url" value="<?= h($movie['trailer_url'] ?? '') ?>">
    </div>
</div>

<div class="form-group">
    <label>Kategoriler</label>
    <div class="checkbox-grid">
        <?php foreach ($categories as $category): ?>
            <label>
                <input
                    type="checkbox"
                    name="categories[]"
                    value="<?= (int)$category['id'] ?>"
                    <?= in_array((int)$category['id'], $selectedCategories, true) ? 'checked' : '' ?>
                >
                <?= h($category['name']) ?>
            </label>
        <?php endforeach; ?>
    </div>
</div>
