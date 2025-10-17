<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteBook;

use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\ValueObject\BookIsbn;
use Psr\EventDispatcher\EventDispatcherInterface;

final class DeleteBook
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(DeleteBookCommand $command): void
    {
        $isbn = BookIsbn::fromString($command->isbn());

        // Necesitamos obtener el libro antes de eliminarlo para tener su título
        $book = $this->bookRepository->findByIsbn($isbn);
        
        if ($book === null) {
            throw BookNotFoundException::withIsbn($isbn->value());
        }

        $this->bookRepository->delete($isbn);
        
        // Despachar evento de libro eliminado
        $this->eventDispatcher->dispatch(
            BookModified::deleted($book->title(), $isbn)
        );
    }
}
