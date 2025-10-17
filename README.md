# Casfid - Books API

REST API for managing books with CRUD operations, JWT authentication, and OpenLibrary integration.

## Architecture

This project follows **Hexagonal Architecture (Ports and Adapters)** with:
- **Domain Layer**: Business logic and entities
- **Application Layer**: Use cases and DTOs
- **Infrastructure Layer**: Controllers, repositories, and external services

## Features

- ✅ CRUD operations for books (Create, Read, Update, Delete)
- ✅ JWT authentication for write operations
- ✅ OpenLibrary API integration for book descriptions
- ✅ Event-driven architecture with domain events
- ✅ Comprehensive unit tests (**30 tests, 83 assertions**, 100% passing)
- ✅ OpenAPI/Swagger documentation
- ✅ Docker support with MySQL database
- ✅ Structured logging system
- ✅ FastRoute for professional routing

## Project Structure

```
casfid-technical-test/
├── src/
│   ├── Application/      # Use cases, commands, queries, DTOs
│   ├── Domain/          # Entities, value objects, domain events
│   └── Infrastructure/  # Controllers, repositories, services
│       ├── Controller/  # REST API controllers
│       ├── Http/        # HTTP application handler
│       ├── Persistence/ # Repository implementations
│       └── Service/     # External services (JWT, OpenLibrary)
├── tests/
│   ├── Fakes/          # Test doubles
│   ├── MotherObject/   # Test data builders
│   └── Unit/           # Unit tests
├── config/
│   ├── Container.php   # Dependency injection configuration
│   ├── Providers.php   # DI providers
│   └── routes.php      # FastRoute route definitions
├── var/
│   ├── cache/          # Cache files (OpenLibrary responses)
│   └── logs/           # Application logs
├── public/             # Web root
│   ├── index.php      # Application entry point
│   └── docs/          # API documentation
└── docker/            # Docker configuration
```

## Endpoints

A Postman collection is included in the root: [postman_collection.json](./postman_collection.json)

### Books API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/api/books` | List all books (supports `?search=` query param) | No |
| `GET` | `/api/books/{isbn}` | Get a book by ISBN | No |
| `POST` | `/api/books` | Create a new book | Yes (JWT) |
| `PUT` | `/api/books/{isbn}` | Update a book by ISBN | Yes (JWT) |
| `DELETE` | `/api/books/{isbn}` | Delete a book by ISBN | Yes (JWT) |

### API Documentation

- **Swagger UI**: http://localhost:8080/docs/
- **OpenAPI Spec**: [public/docs/openapi.yaml](./public/docs/openapi.yaml)

## JWT Authentication

For protected endpoints (POST, PUT, DELETE), include the Authorization header:

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1c2VyMTIzIiwiaWF0IjoxNzU5NDU0OTkxfQ.5L3ghkzsZswyKTYS4Sfao3D_OdM4BK9LD9P8tYtAskU
```

To enable/disable JWT:
```bash
# In .env file
JWT_ENABLED=true  # or false
```

## Logging

Logs are saved to `var/logs/api.log` and include:

- Book lifecycle events (created, updated, deleted)
- OpenLibrary API errors (timeouts, failed requests)
- Application errors and debugging information

Configure log level in `.env`:
```bash
LOG_LEVEL=DEBUG  # DEBUG, INFO, WARNING, ERROR
```

## Dependencies

This project uses the following libraries:

- **nikic/fast-route**: Fast request router for PHP
- **guzzlehttp/guzzle**: HTTP client for external API calls
- **symfony/http-foundation**: HTTP request/response handling
- **php-di/php-di**: Dependency injection container
- **monolog/monolog**: Logging system
- **psr/event-dispatcher**: Event dispatcher interface
- **symfony/event-dispatcher**: Event dispatcher implementation
- **firebase/php-jwt**: JWT authentication
- **phpunit/phpunit**: Unit testing framework
- **vlucas/phpdotenv**: Environment configuration

## Setup

Follow these steps to run the Casfid API locally:

### Prerequisites
- Docker and Docker Compose installed
- Git

### 1. Clone the repository
```bash
git clone https://github.com/luismlg/casfid-technical-test.git
cd casfid-technical-test
```

### 2. Configure environment variables
Create a `.env` file from the example:
```bash
cp .env.example .env
```

Edit `.env` to customize configuration if needed (JWT secret, log level, etc.)

### 3. Build and start the containers
```bash
docker-compose up -d --build
```

This will start:
- **casfid-app**: PHP 8.2 + Apache (port 8080)
- **casfid-db**: MySQL 8.0 (port 3306)

### 4. Install dependencies (if needed)
```bash
docker exec -it casfid-app composer install
```

### 5. Verify the setup
Check that the API is running:
```bash
curl http://localhost:8080/api/books
```

Expected response:
```json
{
  "data": [...],
  "count": 7
}
```

### 6. Access the API
- **API Base URL**: http://localhost:8080
- **API Documentation**: http://localhost:8080/docs/

## Running Tests

Execute the unit test suite:

```bash
# Locally (recommended for development)
vendor/bin/phpunit --testdox

# Or with coverage
vendor/bin/phpunit --coverage-html coverage
```

### Test Results

```
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.29
Configuration: phpunit.xml.dist

..............................                                    30 / 30 (100%)

Time: 00:01.494, Memory: 10.00 MB

✅ Create Book (5 tests)
 ✔ It creates a book
 ✔ It creates a book with description from provider
 ✔ It creates a book with cover url
 ✔ It throws exception when book already exists
 ✔ It creates multiple different books

✅ Delete Book (4 tests)
 ✔ It deletes a book
 ✔ It throws exception when book not found
 ✔ It only deletes the specified book
 ✔ It cannot delete the same book twice

✅ Get Book (3 tests)
 ✔ It returns book when exists
 ✔ It throws exception when book not found
 ✔ It validates isbn format

✅ Get Books (8 tests)
 ✔ It returns empty collection when no books
 ✔ It returns all books
 ✔ It searches by title
 ✔ It searches by author
 ✔ It searches by description
 ✔ It searches is case insensitive
 ✔ It returns multiple matching books
 ✔ It returns empty when search does not match

✅ OpenLibrary Integration (2 tests)
 ✔ It can connect to open library api
 ✔ It handles requests gracefully

✅ Update Book (8 tests)
 ✔ It updates book title
 ✔ It updates book author
 ✔ It updates book year
 ✔ It updates book description
 ✔ It updates book cover url
 ✔ It updates multiple fields at once
 ✔ It throws exception when book not found
 ✔ It does not modify other books

OK! Tests: 30, Assertions: 83
```

### Test Coverage

The test suite includes:

- **Unit Tests**: All use cases (Commands & Queries)
- **Test Doubles**: Fakes for Repository and external services
- **Object Mothers**: Test data builders for clean test setup
- **Edge Cases**: Validations, exceptions, and error handling

Coverage by layer:
- ✅ **Application Layer**: 100% (all Commands and Queries)
- ✅ **Domain Layer**: Value Objects validation
- ✅ **Infrastructure Layer**: External service integration

### Run specific tests
```bash
# Test a specific use case
vendor/bin/phpunit tests/Unit/Application/Command/CreateBook/CreateBookTest.php

# Test with verbose output
vendor/bin/phpunit --testdox

# Generate coverage report
vendor/bin/phpunit --coverage-html coverage
```

## Technical Highlights

### FastRoute Integration
This project uses **FastRoute** (by Nikita Popov) for routing:
- **High performance**: Route matching in O(1) time
- **Industry standard**: Used by Slim Framework, Laravel, and many others
- **Clean API**: Simple and intuitive route definitions
- **PSR-7 compatible**: Works seamlessly with modern PHP standards

Routes are defined in `config/routes.php`:
```php
return function (RouteCollector $r) {
    $r->addRoute('GET', '/api/books', GetBooksController::class);
    $r->addRoute('GET', '/api/books/{isbn}', GetBookController::class);
    $r->addRoute('POST', '/api/books', CreateBookController::class);
    // ...
};
```

### HTTP Application Handler
The `Infrastructure\Http\Application` class provides:
- Request/response lifecycle management
- CORS headers handling
- HTTP error responses (404, 405, 500)
- Clean separation between routing and business logic

### CQRS Pattern
Commands (write operations) and Queries (read operations) are separated:
- **Commands**: `CreateBook`, `UpdateBook`, `DeleteBook`
- **Queries**: `GetBook`, `GetBooks`

### Value Objects
Domain concepts are modeled as immutable Value Objects:
- `BookIsbn`, `BookTitle`, `BookAuthor`, `BookYear`, `BookDescription`
- Self-validating and type-safe

## Example Usage

### Create a book
```bash
curl -X POST http://localhost:8080/api/books \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "isbn": "978-0132350884",
    "year": 2008,
    "description": "A Handbook of Agile Software Craftsmanship"
  }'
```

### Get all books
```bash
curl http://localhost:8080/api/books
```

### Search books
```bash
curl "http://localhost:8080/api/books?search=clean"
```

### Get a specific book
```bash
curl http://localhost:8080/api/books/978-0132350884
```

### Update a book
```bash
curl -X PUT http://localhost:8080/api/books/978-0132350884 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "title": "Clean Code: Updated Edition"
  }'
```

### Delete a book
```bash
curl -X DELETE http://localhost:8080/api/books/978-0132350884 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Author

**Luis A. Cañas Arrones**
- GitHub: [@luismlg](https://github.com/luismlg)
