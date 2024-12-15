<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces;

interface CodeExecutorInterface
{
    /**
     * @param array<string,mixed> $arguments
     */
    public function executeCode(string $code, array $arguments): void;
}
