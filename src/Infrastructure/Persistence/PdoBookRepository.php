<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Book\Book;
use App\Domain\Book\BookCollection;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookIsbn;
use App\Infrastructure\DataTransformer\BookDataTransformer;
use PDO;

final class PdoBookRepository implements BookRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function save(Book $book): void
    {
        $sql = "INSERT INTO books (title, author, isbn, year, description, cover_url, created_at, updated_at) 
                VALUES (:title, :author, :isbn, :year, :description, :cover_url, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    author = VALUES(author),
                    year = VALUES(year),
                    description = VALUES(description),
                    cover_url = VALUES(cover_url),
                    updated_at = NOW()";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $book->title()->value(),
            ':author' => $book->author()->value(),
            ':isbn' => $book->isbn()->value(),
            ':year' => $book->year()->value(),
            ':description' => $book->description()?->value(),
            ':cover_url' => $book->coverUrl(),
        ]);
    }

    public function findByIsbn(BookIsbn $isbn): ?Book
    {
        $sql = "SELECT * FROM books WHERE isbn = :isbn LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':isbn' => $isbn->value()]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return BookDataTransformer::fromArray($data);
    }

    public function exists(BookIsbn $isbn): bool
    {
        return $this->findByIsbn($isbn) !== null;
    }

    public function delete(BookIsbn $isbn): void
    {
        $sql = "DELETE FROM books WHERE isbn = :isbn";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':isbn' => $isbn->value()]);
    }

    public function findAll(): BookCollection
    {
        $sql = "SELECT * FROM books ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        
        $books = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $books[] = BookDataTransformer::fromArray($row);
        }

        return new BookCollection($books);
    }

    public function search(string $search): BookCollection
    {
        $searchTerm = '%' . $search . '%';
        
        $sql = "SELECT * FROM books 
                WHERE title LIKE ? 
                   OR author LIKE ? 
                   OR description LIKE ?
                ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        
        $books = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $books[] = BookDataTransformer::fromArray($row);
        }

        return new BookCollection($books);
    }
}
