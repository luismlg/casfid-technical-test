<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service;

use App\Domain\Book\ValueObject\BookIsbn;
use App\Infrastructure\Service\OpenLibraryBookDescriptionProvider;
use GuzzleHttp\Client;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

final class OpenLibraryBookDescriptionProviderTest extends TestCase
{
    public function testItCanConnectToOpenLibraryApi(): void
    {
        // Arrange
        $logger = new Logger('test');
        $logger->pushHandler(new NullHandler());
        
        $client = new Client(['timeout' => 10]);
        $provider = new OpenLibraryBookDescriptionProvider($client, $logger);
        
        // Using a well-known ISBN that should exist
        $isbn = BookIsbn::fromString('9780132350884'); // Clean Code by Robert Martin

        // Act
        $description = $provider->getDescriptionByIsbn($isbn);

        // Assert
        // We don't assert the exact content because it might change
        // We just verify the service works and can return a description
        $this->assertNotNull($description, 'OpenLibrary should return a description for Clean Code book');
        $this->assertNotEmpty($description->value(), 'Description should not be empty');
    }

    public function testItHandlesRequestsGracefully(): void
    {
        // Arrange
        $logger = new Logger('test');
        $logger->pushHandler(new NullHandler());
        
        $client = new Client(['timeout' => 5]);
        $provider = new OpenLibraryBookDescriptionProvider($client, $logger);
        
        // Using any ISBN - we just want to verify the service doesn't crash
        $isbn = BookIsbn::fromString('9780000000000');

        // Act & Assert - Should not throw exceptions
        $description = $provider->getDescriptionByIsbn($isbn);
        
        // The result can be null or a BookDescription, both are valid
        $this->assertTrue(
            $description === null || $description instanceof \App\Domain\Book\ValueObject\BookDescription,
            'Should return null or BookDescription'
        );
    }
}