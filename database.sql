CREATE DATABASE IF NOT EXISTS film_oneri
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE film_oneri;

DROP TABLE IF EXISTS watched_movies;
DROP TABLE IF EXISTS movie_categories;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    short_description VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    director VARCHAR(150) DEFAULT NULL,
    actors TEXT DEFAULT NULL,
    release_year SMALLINT DEFAULT NULL,
    duration_minutes SMALLINT DEFAULT NULL,
    imdb_score DECIMAL(3,1) DEFAULT NULL,
    poster_path VARCHAR(255) DEFAULT NULL,
    trailer_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE movie_categories (
    movie_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (movie_id, category_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE watched_movies (
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES
('Aksiyon'),
('Bilim Kurgu'),
('Dram'),
('Fantastik'),
('Gerilim'),
('Komedi'),
('Korku'),
('Macera'),
('Romantik'),
('Suç');

INSERT INTO movies
(title, slug, short_description, description, director, actors, release_year, duration_minutes, imdb_score, poster_path, trailer_url)
VALUES
('Inception', 'inception', 'Rüyaların içinde kurulan çok katmanlı bir soygun hikayesi.', 'Dom Cobb, insanların rüyalarına girerek fikir çalan yetenekli bir hırsızdır. Bu kez görevi fikir çalmak değil, bir fikri zihne yerleştirmektir.', 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page', 2010, 148, 8.8, NULL, 'https://www.youtube.com/watch?v=YoHD9XEInc0'),
('Interstellar', 'interstellar', 'İnsanlığın geleceği için yıldızlar arası bir yolculuk.', 'Dünya yaşanmaz hale gelirken bir grup kaşif, insanlık için yeni bir yuva bulmak üzere solucan deliğinden geçerek bilinmeyene yol alır.', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway, Jessica Chastain', 2014, 169, 8.7, NULL, 'https://www.youtube.com/watch?v=zSWdZVtXT7E'),
('The Dark Knight', 'the-dark-knight', 'Batman ve Joker arasında Gotham’ın ruhunu hedef alan savaş.', 'Gotham’da suç düzeni değişirken Joker, Batman’i ahlaki sınırlarının sonuna kadar zorlayan kaotik bir tehdit olarak ortaya çıkar.', 'Christopher Nolan', 'Christian Bale, Heath Ledger, Aaron Eckhart', 2008, 152, 9.0, NULL, 'https://www.youtube.com/watch?v=EXeTwQWrcwY');

INSERT INTO movie_categories (movie_id, category_id)
SELECT m.id, c.id
FROM movies m
JOIN categories c ON c.name IN ('Bilim Kurgu', 'Aksiyon')
WHERE m.slug = 'inception';

INSERT INTO movie_categories (movie_id, category_id)
SELECT m.id, c.id
FROM movies m
JOIN categories c ON c.name IN ('Bilim Kurgu', 'Dram', 'Macera')
WHERE m.slug = 'interstellar';

INSERT INTO movie_categories (movie_id, category_id)
SELECT m.id, c.id
FROM movies m
JOIN categories c ON c.name IN ('Aksiyon', 'Suç', 'Gerilim')
WHERE m.slug = 'the-dark-knight';
