-- init.sql - Script de inicialización de la base de datos
-- Este script se ejecuta automáticamente cuando se crea el contenedor de MySQL

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS casfid
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE casfid;

-- Tabla de libros
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    year INT NOT NULL,
    description TEXT DEFAULT NULL,
    cover_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_isbn (isbn),
    INDEX idx_author (author),
    INDEX idx_title (title),
    INDEX idx_year (year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar libros de Harry Potter
INSERT INTO books (title, author, isbn, year, description, cover_url) VALUES
(
    'Harry Potter and the Philosopher''s Stone',
    'J.K. Rowling',
    '978-0747532699',
    1997,
    'First book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780747532699-L.jpg'
),
(
    'Harry Potter and the Chamber of Secrets',
    'J.K. Rowling',
    '978-0747538493',
    1998,
    'Second book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780747538493-L.jpg'
),
(
    'Harry Potter and the Prisoner of Azkaban',
    'J.K. Rowling',
    '978-0747542155',
    1999,
    'Third book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780747542155-L.jpg'
),
(
    'Harry Potter and the Goblet of Fire',
    'J.K. Rowling',
    '978-0747546245',
    2000,
    'Fourth book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780747546245-L.jpg'
),
(
    'Harry Potter and the Order of the Phoenix',
    'J.K. Rowling',
    '978-0747551003',
    2003,
    'Fifth book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780747551003-L.jpg'
),
(
    'Harry Potter and the Half-Blood Prince',
    'J.K. Rowling',
    '978-0747581086',
    2005,
    'Sixth book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780747581086-L.jpg'
),
(
    'Harry Potter and the Deathly Hallows',
    'J.K. Rowling',
    '978-0545010221',
    2007,
    'Seventh and final book of the Harry Potter series.',
    'https://covers.openlibrary.org/b/isbn/9780545010221-L.jpg'
)
ON DUPLICATE KEY UPDATE id=id; -- No duplicar si ya existen

-- Verificar inserción
SELECT COUNT(*) as total_books FROM books;
