<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command\UpdateBook;

use App\Application\Command\UpdateBook\UpdateBook;
use App\Application\Command\UpdateBook\UpdateBookCommand;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Exception\BookNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\Fakes\FakeBookRepository;
use Tests\Fakes\FakeEventDispatcher;
use Tests\MotherObject\Book\BookMotherObject;

final class UpdateBookTest extends TestCase
{
    private FakeBookRepository $bookRepository;
    private FakeEventDispatcher $eventDispatcher;
    private UpdateBook $useCase;

    protected function setUp(): void
    {
        $this->bookRepository = new FakeBookRepository();
        $this->eventDispatcher = new FakeEventDispatcher();
        $this->useCase = new UpdateBook($this->bookRepository, $this->eventDispatcher);
    }

    public function testItUpdatesBookTitle(): void
    {
        // Arrange
        $book = BookMotherObject::cleanCode();
        $this->bookRepository->save($book);

        $command = new UpdateBookCommand(
            isbn: $book->isbn()->value(),
            title: 'Clean Code - Updated Edition',
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($updatedBook);
        $this->assertEquals('Clean Code - Updated Edition', $updatedBook->title()->value());
        $this->assertEquals($book->author()->value(), $updatedBook->author()->value());
        
        // Assert Event
        $this->assertTrue($this->eventDispatcher->hasDispatched(BookModified::class));
        $events = $this->eventDispatcher->getEventsOfType(BookModified::class);
        $this->assertCount(1, $events);
        
        /** @var BookModified $event */
        $event = $events[0];
        $this->assertEquals('updated', $event->action);
    }

    public function testItUpdatesBookAuthor(): void
    {
        // Arrange
        $book = BookMotherObject::pragmaticProgrammer();
        $this->bookRepository->save($book);

        $command = new UpdateBookCommand(
            isbn: $book->isbn()->value(),
            author: 'Andrew Hunt, David Thomas & Others',
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($updatedBook);
        $this->assertEquals('Andrew Hunt, David Thomas & Others', $updatedBook->author()->value());
    }

    public function testItUpdatesBookYear(): void
    {
        // Arrange
        $book = BookMotherObject::create();
        $this->bookRepository->save($book);

        $command = new UpdateBookCommand(
            isbn: $book->isbn()->value(),
            year: 2024,
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($updatedBook);
        $this->assertEquals(2024, $updatedBook->year()->value());
    }

    public function testItUpdatesBookDescription(): void
    {
        // Arrange
        $book = BookMotherObject::withoutDescription();
        $this->bookRepository->save($book);

        $command = new UpdateBookCommand(
            isbn: $book->isbn()->value(),
            description: 'A newly added description for this book.',
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($updatedBook);
        $this->assertNotNull($updatedBook->description());
        $this->assertEquals('A newly added description for this book.', $updatedBook->description()->value());
    }

    public function testItUpdatesBookCoverUrl(): void
    {
        // Arrange
        $book = BookMotherObject::create();
        $this->bookRepository->save($book);

        $command = new UpdateBookCommand(
            isbn: $book->isbn()->value(),
            coverUrl: 'https://example.com/new-cover.jpg',
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($updatedBook);
        $this->assertEquals('https://example.com/new-cover.jpg', $updatedBook->coverUrl());
    }

    public function testItUpdatesMultipleFieldsAtOnce(): void
    {
        // Arrange
        $book = BookMotherObject::create();
        $this->bookRepository->save($book);

        $command = new UpdateBookCommand(
            isbn: $book->isbn()->value(),
            title: 'Updated Title',
            author: 'Updated Author',
            year: 2023,
            description: 'Updated description',
            coverUrl: 'https://example.com/updated.jpg',
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($updatedBook);
        $this->assertEquals('Updated Title', $updatedBook->title()->value());
        $this->assertEquals('Updated Author', $updatedBook->author()->value());
        $this->assertEquals(2023, $updatedBook->year()->value());
        $this->assertEquals('Updated description', $updatedBook->description()->value());
        $this->assertEquals('https://example.com/updated.jpg', $updatedBook->coverUrl());
    }

    public function testItThrowsExceptionWhenBookNotFound(): void
    {
        // Arrange
        $command = new UpdateBookCommand(
            isbn: '978-9999999999',
            title: 'This book does not exist',
        );

        // Assert
        $this->expectException(BookNotFoundException::class);
        $this->expectExceptionMessage('not found');

        // Act
        $this->useCase->execute($command);
    }

    public function testItDoesNotModifyOtherBooks(): void
    {
        // Arrange
        $book1 = BookMotherObject::cleanCode();
        $book2 = BookMotherObject::pragmaticProgrammer();

        $this->bookRepository->save($book1);
        $this->bookRepository->save($book2);

        $command = new UpdateBookCommand(
            isbn: $book1->isbn()->value(),
            title: 'Updated Clean Code',
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $updatedBook1 = $this->bookRepository->findByIsbn($book1->isbn());
        $unchangedBook2 = $this->bookRepository->findByIsbn($book2->isbn());

        $this->assertEquals('Updated Clean Code', $updatedBook1->title()->value());
        $this->assertEquals($book2->title()->value(), $unchangedBook2->title()->value());
    }
}
