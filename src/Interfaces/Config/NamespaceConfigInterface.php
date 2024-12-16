<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Config;

use Prosopo\Views\Interfaces\Modules\ModulesInterface;

interface NamespaceConfigInterface
{
    //// Getters.

    public function getTemplatesRootPath(): string;

    public function getModelsRootNamespace(): string;

    public function getTemplateFileExtension(): string;

    /**
     * @return callable(array<string,mixed> $eventDetails): void|null
     */
    public function getTemplateErrorHandler(): ?callable;

    public function getTemplateErrorEventName(): string;

    /**
     * @return array<string,mixed>
     */
    public function getDefaultPropertyValues(): array;

    public function getModules(): ModulesInterface;

    //// Setters.

    public function setTemplatesRootPath(string $templatesRootPath): self;

    public function setModelsRootNamespace(string $modelsRootNamespace): self;

    public function setTemplateFileExtension(string $templateFileExtension): self;

    /**
     * @param callable(array<string,mixed> $eventDetails): void|null $templateErrorHandler
     */
    public function setTemplateErrorHandler(?callable $templateErrorHandler): self;

    /**
     * @param array<string,mixed> $defaultPropertyValues
     */
    public function setDefaultPropertyValues(array $defaultPropertyValues): self;

    public function setTemplateErrorEventName(string $templateErrorEventName): self;
}
