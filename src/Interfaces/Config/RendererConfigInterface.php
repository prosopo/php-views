<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Config;

use Prosopo\Views\Interfaces\Modules\RendererModulesContainerInterface;

interface RendererConfigInterface extends RendererModulesContainerInterface
{
    //// Getters.

    /**
     * @return callable(array<string,mixed> $eventDetails):void|null
     */
    public function getTemplateErrorHandler(): ?callable;

    public function getTemplateErrorEventName(): string;

    /**
     * @return array<string,mixed>
     */
    public function getGlobalVariables(): array;

    //// Setters.

    /**
     * @param callable(array<string,mixed> $eventDetails):void|null $templateErrorHandler
     */
    public function setTemplateErrorHandler(?callable $templateErrorHandler): self;

    public function setTemplateErrorEventName(string $templateErrorEventName): self;

    /**
     * @param array<string,mixed> $globalVariables
     */
    public function setGlobalVariables(array $globalVariables): self;
}
