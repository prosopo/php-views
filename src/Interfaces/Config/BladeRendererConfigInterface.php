<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Config;

interface BladeRendererConfigInterface extends RendererConfigInterface
{
    //// Getters.

    public function isFileBasedTemplate(): bool;

    /**
     * @return callable(mixed $variable): string|null
     */
    public function getCustomOutputEscapeCallback(): ?callable;

    public function getEscapeVariableName(): string;

    /**
     * @return callable(string $template): string|null
     */
    public function getCompilerExtensionCallback(): ?callable;

    //// Setters.

    public function setIsFileBasedTemplate(bool $isFileBasedTemplate): self;

    /**
     * @param callable(string $template): string|null $compilerExtensionCallback
     */
    public function setCompilerExtensionCallback(?callable $compilerExtensionCallback): self;

    public function setCustomOutputEscapeCallback(?callable $customOutputEscapeCallback): self;

    public function setEscapeVariableName(string $escapeVariableName): self;
}
