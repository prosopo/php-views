<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeRunner;

use Prosopo\Views\Interfaces\CodeRunnerInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class CodeRunnerWithGlobalArguments implements CodeRunnerInterface
{
    private CodeRunnerInterface $codeExecutor;
    /**
     * @var array<string,mixed>
     */
    private array $globalArguments;

    /**
     * @param array<string,mixed> $globalArguments
     */
    public function __construct(CodeRunnerInterface $codeExecutor, array $globalArguments)
    {
        $this->codeExecutor = $codeExecutor;
        $this->globalArguments = $globalArguments;
    }

    public function runCode(string $code, array $arguments = []): void
    {
        $arguments = array_merge($this->globalArguments, $arguments);

        $this->codeExecutor->runCode($code, $arguments);
    }
}
