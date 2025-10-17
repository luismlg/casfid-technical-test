<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command\DeleteBook;

use App\Application\Command\DeleteBook\DeleteBook;
use App\Application\Command\DeleteBook\DeleteBookCommand;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Exception\BookNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\Fakes\FakeBookRepository;
use Tests\Fakes\FakeEventDispatcher;
use Tests\MotherObject\Book\BookMotherObject;

final class DeleteBookTest extends TestCase
{
    private FakeBookRepository $bookRepository;
    private FakeEventDispatcher $eventDispatcher;
    private DeleteBook $useCase;

    protected function setUp(): void
    {
        $this->bookRepository = new FakeBookRepository();
        $this->eventDispatcher = new FakeEventDispatcher();
        $this->useCase = new DeleteBook($this->bookRepository, $this->eventDispatcher);
    }

    public function testItDeletesABook(): void
    {
        // Arrange
        $book = BookMotherObject::cleanCode();
        $this->bookRepository->save($book);

        $command = new DeleteBookCommand(
            isbn: $book->isbn()->value()
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $deletedBook = $this->bookRepository->findByIsbn($book->isbn());
        $this->assertNull($deletedBook);
        
        // Assert Event
        $this->assertTrue($this->eventDispatcher->hasDispatched(BookModified::class));
        $events = $this->eventDispatcher->getEventsOfType(BookModified::class);
        $this->assertCount(1, $events);
        
        /** @var BookModified $event */
        $event = $events[0];
        $this->assertEquals('deleted', $event->action);
        $this->assertEquals($book->isbn()->value(), $event->isbn->value());
    }

    public function testItThrowsExceptionWhenBookNotFound(): void
    {
        // Arrange
        $command = new DeleteBookCommand(
            isbn: '978-9999999999'
        );

        // Assert
        $this->expectException(BookNotFoundException::class);
        $this->expectExceptionMessage('not found');

        // Act
        $this->useCase->execute($command);
    }

    public function testItOnlyDeletesTheSpecifiedBook(): void
    {
        // Arrange
        $book1 = BookMotherObject::cleanCode();
        $book2 = BookMotherObject::pragmaticProgrammer();

        $this->bookRepository->save($book1);
        $this->bookRepository->save($book2);

        $command = new DeleteBookCommand(
            isbn: $book1->isbn()->value()
        );

        // Act
        $this->useCase->execute($command);

        // Assert
        $deletedBook = $this->bookRepository->findByIsbn($book1->isbn());
        $remainingBook = $this->bookRepository->findByIsbn($book2->isbn());

        $this->assertNull($deletedBook);
        $this->assertNotNull($remainingBook);
        $this->assertEquals($book2->title()->value(), $remainingBook->title()->value());
    }

    public function testItCannotDeleteTheSameBookTwice(): void
    {
        // Arrange
        $book = BookMotherObject::create();
        $this->bookRepository->save($book);

        $command = new DeleteBookCommand(
            isbn: $book->isbn()->value()
        );

        // Act - First deletion (should succeed)
        $this->useCase->execute($command);

        // Assert - Second deletion should fail
        $this->expectException(BookNotFoundException::class);

        $this->useCase->execute($command);
    }
}
