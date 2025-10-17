<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Book\Event\BookModified;
use Psr\Log\LoggerInterface;

/**
 * BookModifiedListener
 * 
 * Listener que reacciona a eventos BookModified del dominio.
 * Actualmente registra los cambios en el sistema de logs para auditoría y trazabilidad.
 * 
 * Este listener es extensible y podría incorporar en el futuro:
 * - Envío de notificaciones push o email a usuarios suscritos
 * - Invalidación automática de cachés relacionados con el libro modificado
 * - Actualización de índices de búsqueda (Elasticsearch, Algolia, etc.)
 * - Disparo de webhooks para integraciones externas
 * - Publicación de eventos en message brokers (RabbitMQ, Kafka)
 * - Generación de reportes de actividad en tiempo real
 * 
 * Patrón utilizado: Observer/Event Listener con PSR-14
 */
final readonly class BookModifiedListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * Maneja el evento BookModified registrando la operación en logs
     * 
     * @param BookModified $event El evento de dominio con información del libro modificado
     */
    public function __invoke(BookModified $event): void
    {
        $message = sprintf(
            'Book "%s" (ISBN: %s) %s successfully',
            $event->title->value(),
            $event->isbn->value(),
            $event->action
        );

        $this->logger->info($message, [
            'isbn' => $event->isbn->value(),
            'title' => $event->title->value(),
            'action' => $event->action,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
