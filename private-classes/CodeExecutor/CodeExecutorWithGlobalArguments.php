<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeExecutor;

use Prosopo\Views\Interfaces\CodeExecutorInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class CodeExecutorWithGlobalArguments implements CodeExecutorInterface
{
    private CodeExecutorInterface $codeExecutor;
    /**
     * @var array<string,mixed>
     */
    private array $globalArguments;

    /**
     * @param array<string,mixed> $globalArguments
     */
    public function __construct(CodeExecutorInterface $codeExecutor, array $globalArguments)
    {
        $this->codeExecutor = $codeExecutor;
        $this->globalArguments = $globalArguments;
    }

    public function executeCode(string $code, array $arguments = []): void
    {
        $arguments = array_merge($this->globalArguments, $arguments);

        $this->codeExecutor->executeCode($code, $arguments);
    }
}
