<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces;

interface EventDispatcherInterface
{
    /**
     * @param array<string,mixed> $eventDetails
     */
    public function dispatchEvent(string $eventName, array $eventDetails): void;

    /**
     * @param callable(array<string,mixed> $eventDetails):void $eventListener
     */
    public function addEventListener(string $eventName, callable $eventListener): void;

    /**
     * @param callable(array<string,mixed> $eventDetails):void $eventListener
     */
    public function removeEventListener(string $eventName, callable $eventListener): void;

    /**
     * @param array<string,mixed> $eventDetails
     */
    public function registerEventDetails(string $eventName, array $eventDetails): void;

    /**
     * @param array<string,mixed> $eventDetails
     */
    public function unregisterEventDetails(string $eventName, array $eventDetails): void;
}
