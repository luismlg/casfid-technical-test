<?php

declare(strict_types=1);

namespace App\Application\Command\CreateBook;

use App\Domain\Book\Book;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Exception\BookAlreadyExistsException;
use App\Domain\Book\Service\BookDescriptionProviderInterface;
use App\Domain\Book\ValueObject\BookTitle;
use App\Domain\Book\ValueObject\BookAuthor;
use App\Domain\Book\ValueObject\BookIsbn;
use App\Domain\Book\ValueObject\BookYear;
use App\Domain\Book\ValueObject\BookDescription;
use Psr\EventDispatcher\EventDispatcherInterface;

final class CreateBook
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly BookDescriptionProviderInterface $bookDescriptionProvider,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(CreateBookCommand $command): void
    {
        $title = BookTitle::fromString($command->title());
        $author = BookAuthor::fromString($command->author());
        $isbn = BookIsbn::fromString($command->isbn());
        $year = BookYear::fromInt($command->year());

        // Verificar si ya existe
        if ($this->bookRepository->exists($isbn)) {
            throw BookAlreadyExistsException::withIsbn($isbn->value());
        }

        // Obtener descripción desde OpenLibrary API
        $description = $this->bookDescriptionProvider->getDescriptionByIsbn($isbn);

        $book = new Book(
            $title,
            $author,
            $isbn,
            $year,
            $description,
            $command->coverUrl()
        );

        $this->bookRepository->save($book);
        
        // Despachar evento de libro creado
        $this->eventDispatcher->dispatch(
            BookModified::created($title, $isbn)
        );
    }
}
