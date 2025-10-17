<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateBook;

use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\ValueObject\BookIsbn;
use App\Domain\Book\ValueObject\BookTitle;
use App\Domain\Book\ValueObject\BookAuthor;
use App\Domain\Book\ValueObject\BookYear;
use App\Domain\Book\ValueObject\BookDescription;
use Psr\EventDispatcher\EventDispatcherInterface;

final class UpdateBook
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(UpdateBookCommand $command): void
    {
        $isbn = BookIsbn::fromString($command->isbn());

        $book = $this->bookRepository->findByIsbn($isbn);
        
        if ($book === null) {
            throw BookNotFoundException::withIsbn($isbn->value());
        }

        // Actualizar campos si vienen en el command
        if ($command->title() !== null) {
            $book->updateTitle(BookTitle::fromString($command->title()));
        }

        if ($command->author() !== null) {
            $book->updateAuthor(BookAuthor::fromString($command->author()));
        }

        if ($command->year() !== null) {
            $book->updateYear(BookYear::fromInt($command->year()));
        }

        if ($command->description() !== null) {
            $book->updateDescription(BookDescription::fromString($command->description()));
        }

        if ($command->coverUrl() !== null) {
            $book->updateCoverUrl($command->coverUrl());
        }

        $this->bookRepository->save($book);
        
        // Despachar evento de libro actualizado
        $this->eventDispatcher->dispatch(
            BookModified::updated($book->title(), $book->isbn())
        );
    }
}
