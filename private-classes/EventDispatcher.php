<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses;

use Prosopo\Views\Interfaces\EventDispatcherInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string,array<int,callable(array<string,mixed> $details):void>> name => listeners
     */
    private array $eventListeners;
    /**
     * @var array<string, array<string,mixed>> name => details
     */
    private array $eventDetails;

    public function __construct()
    {
        $this->eventListeners = [];
        $this->eventDetails = [];
    }

    public function dispatchEvent(string $eventName, array $eventDetails): void
    {
        $eventListeners = $this->getEventListeners($eventName);
        $eventDetails = array_merge($this->getEventDetails($eventName), $eventDetails);

        array_walk($eventListeners, function (callable $listener) use ($eventDetails) {
            $listener($eventDetails);
        });
    }

    public function addEventListener(string $eventName, callable $eventListener): void
    {
        $eventListeners = $this->getEventListeners($eventName);

        $eventListeners[] = $eventListener;

        $this->eventListeners[$eventName] = $eventListeners;
    }

    public function removeEventListener(string $eventName, callable $eventListener): void
    {
        $eventListeners = $this->getEventListeners($eventName);

        $index = array_search($eventListener, $eventListeners, true);

        if (false === $index) {
            return;
        }

        unset($eventListeners[$index]);

        // Reindex.
        $eventListeners = array_values($eventListeners);

        $this->eventListeners[$eventName] = $eventListeners;
    }

    public function registerEventDetails(string $eventName, array $eventDetails): void
    {
        $eventDetails = array_merge($this->getEventDetails($eventName), $eventDetails);

        $this->eventDetails[$eventName] = $eventDetails;
    }

    public function unregisterEventDetails(string $eventName, array $eventDetails): void
    {
        $eventDetails = array_diff_key($this->getEventDetails($eventName), $eventDetails);

        $this->eventDetails[$eventName] = $eventDetails;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getEventDetails(string $eventName): array
    {
        return key_exists($eventName, $this->eventDetails) ?
            $this->eventDetails[$eventName] :
            [];
    }

    /**
     * @return array<int,callable(array<string,mixed> $details):void>
     */
    protected function getEventListeners(string $eventName): array
    {
        return key_exists($eventName, $this->eventListeners) ?
            $this->eventListeners[$eventName] :
            [];
    }
}
