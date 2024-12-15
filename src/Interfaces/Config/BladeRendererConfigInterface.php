<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Config;

interface BladeRendererConfigInterface extends RendererConfigInterface
{
    //// Getters.

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

    /**
     * @param callable(string $template): string|null $compilerExtensionCallback
     */
    public function setCompilerExtensionCallback(?callable $compilerExtensionCallback): self;

    public function setCustomOutputEscapeCallback(?callable $customOutputEscapeCallback): self;

    public function setEscapeVariableName(string $escapeVariableName): self;
}
