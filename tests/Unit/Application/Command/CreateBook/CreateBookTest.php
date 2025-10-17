<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command\CreateBook;

use App\Application\Command\CreateBook\CreateBook;
use App\Application\Command\CreateBook\CreateBookCommand;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Exception\BookAlreadyExistsException;
use App\Domain\Book\Service\BookDescriptionProviderInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\Fakes\FakeBookDescriptionProvider;
use Tests\Fakes\FakeBookRepository;
use Tests\Fakes\FakeEventDispatcher;
use Tests\MotherObject\Book\BookMotherObject;

final class CreateBookTest extends TestCase
{
    private FakeBookRepository $bookRepository;
    private FakeBookDescriptionProvider $bookDescriptionProvider;
    private FakeEventDispatcher $eventDispatcher;
    private CreateBook $useCase;

    protected function setUp(): void
    {
        $this->bookRepository = new FakeBookRepository();
        $this->bookDescriptionProvider = new FakeBookDescriptionProvider();
        $this->eventDispatcher = new FakeEventDispatcher();
        $this->useCase = new CreateBook($this->bookRepository, $this->bookDescriptionProvider, $this->eventDispatcher);
    }

    public function testItCreatesABook(): void
    {
        // Arrange
        $book = BookMotherObject::create();

        $command = new CreateBookCommand(
            title: $book->title()->value(),
            author: $book->author()->value(),
            isbn: $book->isbn()->value(),
            year: $book->year()->value(),
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $savedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($savedBook);
        $this->assertEquals($book->title()->value(), $savedBook->title()->value());
        $this->assertEquals($book->author()->value(), $savedBook->author()->value());
        $this->assertEquals($book->isbn()->value(), $savedBook->isbn()->value());
        $this->assertEquals($book->year()->value(), $savedBook->year()->value());
        
        // Assert Event
        $this->assertTrue($this->eventDispatcher->hasDispatched(BookModified::class));
        $events = $this->eventDispatcher->getEventsOfType(BookModified::class);
        $this->assertCount(1, $events);
        
        /** @var BookModified $event */
        $event = $events[0];
        $this->assertEquals('created', $event->action);
        $this->assertEquals($book->isbn()->value(), $event->isbn->value());
        $this->assertEquals($book->title()->value(), $event->title->value());
    }

    public function testItCreatesABookWithDescriptionFromProvider(): void
    {
        // Arrange
        $book = BookMotherObject::withDescription();

        $command = new CreateBookCommand(
            title: $book->title()->value(),
            author: $book->author()->value(),
            isbn: $book->isbn()->value(),
            year: $book->year()->value(),
        );

        // Configure fake provider to return description for this ISBN
        $this->bookDescriptionProvider->save($book->isbn(), $book->description());

        // Act
        $this->useCase->execute($command);

        // Assert
        $savedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($savedBook);
        $this->assertNotNull($savedBook->description());
        $this->assertEquals($book->description()->value(), $savedBook->description()->value());
        
        // Assert Event
        $this->assertTrue($this->eventDispatcher->hasDispatched(BookModified::class));
    }

    public function testItCreatesABookWithCoverUrl(): void
    {
        // Arrange
        $book = BookMotherObject::create(
            coverUrl: 'https://example.com/cover.jpg'
        );

        $command = new CreateBookCommand(
            title: $book->title()->value(),
            author: $book->author()->value(),
            isbn: $book->isbn()->value(),
            year: $book->year()->value(),
            coverUrl: $book->coverUrl(),
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $savedBook = $this->bookRepository->findByIsbn($book->isbn());

        $this->assertNotNull($savedBook);
        $this->assertEquals('https://example.com/cover.jpg', $savedBook->coverUrl());
        
        // Assert Event
        $this->assertTrue($this->eventDispatcher->hasDispatched(BookModified::class));
    }

    public function testItThrowsExceptionWhenBookAlreadyExists(): void
    {
        // Arrange
        $book = BookMotherObject::cleanCode();

        $command = new CreateBookCommand(
            title: $book->title()->value(),
            author: $book->author()->value(),
            isbn: $book->isbn()->value(),
            year: $book->year()->value(),
        );

        // Act - First creation (should succeed)
        $this->useCase->execute($command);

        // Assert - Second creation should fail
        $this->expectException(BookAlreadyExistsException::class);
        $this->expectExceptionMessage('already exists');

        $this->useCase->execute($command);
    }

    public function testItCreatesMultipleDifferentBooks(): void
    {
        // Arrange
        $cleanCode = BookMotherObject::cleanCode();
        $pragmaticProgrammer = BookMotherObject::pragmaticProgrammer();

        $command1 = new CreateBookCommand(
            title: $cleanCode->title()->value(),
            author: $cleanCode->author()->value(),
            isbn: $cleanCode->isbn()->value(),
            year: $cleanCode->year()->value(),
        );

        $command2 = new CreateBookCommand(
            title: $pragmaticProgrammer->title()->value(),
            author: $pragmaticProgrammer->author()->value(),
            isbn: $pragmaticProgrammer->isbn()->value(),
            year: $pragmaticProgrammer->year()->value(),
        );

        // Act
        $this->useCase->execute($command1);
        $this->useCase->execute($command2);

        // Assert
        $savedBook1 = $this->bookRepository->findByIsbn($cleanCode->isbn());
        $savedBook2 = $this->bookRepository->findByIsbn($pragmaticProgrammer->isbn());

        $this->assertNotNull($savedBook1);
        $this->assertNotNull($savedBook2);
        $this->assertNotEquals($savedBook1->isbn()->value(), $savedBook2->isbn()->value());
        
        // Assert Events (2 libros creados = 2 eventos)
        $this->assertEquals(2, $this->eventDispatcher->count());
        $this->assertCount(2, $this->eventDispatcher->getEventsOfType(BookModified::class));
    }
}
