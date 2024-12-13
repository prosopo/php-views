<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

interface TemplateErrorInterface
{
    public function getTemplate(): string;

    public function getCompiledPhpTemplate(): string;

    public function getMessage(): string;

    public function getLine(): int;

    /**
     * @return array<string,mixed>
     */
    public function getVariables(): array;
}
