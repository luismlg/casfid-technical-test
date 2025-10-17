<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Query\GetBook;

use App\Application\Query\GetBook\GetBook;
use App\Application\Query\GetBook\GetBookQuery;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\ValueObject\Isbn;
use Tests\Fakes\FakeBookRepository;
use Tests\MotherObject\Book\BookMotherObject;
use PHPUnit\Framework\TestCase;

final class GetBookTest extends TestCase
{
    private FakeBookRepository $repository;
    private GetBook $useCase;

    protected function setUp(): void
    {
        $this->repository = new FakeBookRepository();
        $this->useCase = new GetBook($this->repository);
    }

    protected function tearDown(): void
    {
        $this->repository->clear();
    }

    public function testItReturnsBookWhenExists(): void
    {
        // Arrange
        $book = BookMotherObject::create(isbn: '978-0-123456-47-2');
        $this->repository->save($book);
        
        $query = new GetBookQuery(isbn: '978-0-123456-47-2');

        // Act
        $response = $this->useCase->execute($query);

        // Assert
        $bookDto = $response->book();
        self::assertSame('978-0-123456-47-2', $bookDto->isbn);
        self::assertSame($book->title()->value(), $bookDto->title);
        self::assertSame($book->author()->value(), $bookDto->author);
        self::assertSame($book->year()->value(), $bookDto->year);
        self::assertSame($book->description()?->value(), $bookDto->description);
        self::assertSame($book->coverUrl(), $bookDto->coverUrl);
    }

    public function testItThrowsExceptionWhenBookNotFound(): void
    {
        // Arrange
        $query = new GetBookQuery(isbn: '978-0-000000-00-0');

        // Assert
        $this->expectException(BookNotFoundException::class);
        $this->expectExceptionMessage('Book with ISBN "978-0-000000-00-0" not found');

        // Act
        $this->useCase->execute($query);
    }

    public function testItValidatesIsbnFormat(): void
    {
        // Arrange
        $query = new GetBookQuery(isbn: 'invalid-isbn');

        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        $this->useCase->execute($query);
    }
}
