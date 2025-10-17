<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Query\GetBooks;

use App\Application\Query\GetBooks\GetBooks;
use App\Application\Query\GetBooks\GetBooksQuery;
use App\Domain\Book\BookRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeBookRepository;
use Tests\MotherObject\Book\BookMotherObject;

final class GetBooksTest extends TestCase
{
    private FakeBookRepository $bookRepository;
    private GetBooks $useCase;

    protected function setUp(): void
    {
        $this->bookRepository = new FakeBookRepository();
        $this->useCase = new GetBooks($this->bookRepository);
    }

    protected function tearDown(): void
    {
        if ($this->bookRepository instanceof FakeBookRepository) {
            $this->bookRepository->clear();
        }
    }

    public function testItReturnsEmptyCollectionWhenNoBooks(): void
    {
        // Arrange
        $query = new GetBooksQuery();

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $this->assertEquals(0, $response->books()->count());
    }

    public function testItReturnsAllBooks(): void
    {
        // Arrange
        $book1 = BookMotherObject::cleanCode();
        $book2 = BookMotherObject::pragmaticProgrammer();
        $book3 = BookMotherObject::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '978-0132350885',
            year: 2020
        );

        $this->bookRepository->save($book1);
        $this->bookRepository->save($book2);
        $this->bookRepository->save($book3);

        $query = new GetBooksQuery();

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $this->assertEquals(3, $response->books()->count());
    }

    public function testItSearchesByTitle(): void
    {
        // Arrange
        $cleanCode = BookMotherObject::cleanCode();
        $pragmatic = BookMotherObject::pragmaticProgrammer();

        $this->bookRepository->save($cleanCode);
        $this->bookRepository->save($pragmatic);

        $query = new GetBooksQuery(search: 'Clean');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $books = $response->books()->toArray();
        $this->assertCount(1, $books);
        $this->assertEquals('Clean Code', $books[0]['title']);
    }

    public function testItSearchesByAuthor(): void
    {
        // Arrange
        $cleanCode = BookMotherObject::cleanCode();
        $pragmatic = BookMotherObject::pragmaticProgrammer();

        $this->bookRepository->save($cleanCode);
        $this->bookRepository->save($pragmatic);

        $query = new GetBooksQuery(search: 'Martin');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $books = $response->books()->toArray();
        $this->assertCount(1, $books);
        $this->assertEquals('Robert C. Martin', $books[0]['author']);
    }

    public function testItSearchesByDescription(): void
    {
        // Arrange
        $book1 = BookMotherObject::create(
            title: 'Book One',
            author: 'Author One',
            isbn: '978-1111111111',
            year: 2020,
            description: 'This book talks about testing methodologies'
        );

        $book2 = BookMotherObject::create(
            title: 'Book Two',
            author: 'Author Two',
            isbn: '978-2222222222',
            year: 2021,
            description: 'This book is about design patterns'
        );

        $this->bookRepository->save($book1);
        $this->bookRepository->save($book2);

        $query = new GetBooksQuery(search: 'testing');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $books = $response->books()->toArray();
        $this->assertCount(1, $books);
        $this->assertEquals('Book One', $books[0]['title']);
    }

    public function testItSearchesIsCaseInsensitive(): void
    {
        // Arrange
        $book = BookMotherObject::cleanCode();
        $this->bookRepository->save($book);

        $query = new GetBooksQuery(search: 'CLEAN');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $this->assertEquals(1, $response->books()->count());
    }

    public function testItReturnsMultipleMatchingBooks(): void
    {
        // Arrange
        $book1 = BookMotherObject::create(
            title: 'Clean Code',
            author: 'Robert Martin',
            isbn: '978-1111111111',
            year: 2008
        );

        $book2 = BookMotherObject::create(
            title: 'The Clean Coder',
            author: 'Robert Martin',
            isbn: '978-2222222222',
            year: 2011
        );

        $book3 = BookMotherObject::create(
            title: 'Design Patterns',
            author: 'Gang of Four',
            isbn: '978-3333333333',
            year: 1994
        );

        $this->bookRepository->save($book1);
        $this->bookRepository->save($book2);
        $this->bookRepository->save($book3);

        $query = new GetBooksQuery(search: 'Clean');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $books = $response->books()->toArray();
        $this->assertCount(2, $books);
    }

    public function testItReturnsEmptyWhenSearchDoesNotMatch(): void
    {
        // Arrange
        $book = BookMotherObject::cleanCode();
        $this->bookRepository->save($book);

        $query = new GetBooksQuery(search: 'NonExistentBook');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $this->assertEquals(0, $response->books()->count());
    }
}
