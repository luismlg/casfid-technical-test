<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * FakeEventDispatcher para tests
 * 
 * Implementación simple que almacena los eventos despachados
 * para poder verificarlos en los tests.
 */
final class FakeEventDispatcher implements EventDispatcherInterface
{
    /** @var object[] */
    public array $dispatchedEvents = [];

    public function dispatch(object $event): object
    {
        $this->dispatchedEvents[] = $event;
        return $event;
    }

    /**
     * Helper method: Verifica si se despachó un evento de cierto tipo
     */
    public function hasDispatched(string $eventClass): bool
    {
        foreach ($this->dispatchedEvents as $event) {
            if ($event instanceof $eventClass) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper method: Obtiene todos los eventos de un tipo específico
     * @return object[]
     */
    public function getEventsOfType(string $eventClass): array
    {
        return array_filter(
            $this->dispatchedEvents,
            fn($event) => $event instanceof $eventClass
        );
    }

    /**
     * Helper method: Limpia todos los eventos
     */
    public function clear(): void
    {
        $this->dispatchedEvents = [];
    }

    /**
     * Helper method: Cuenta eventos despachados
     */
    public function count(): int
    {
        return count($this->dispatchedEvents);
    }
}
