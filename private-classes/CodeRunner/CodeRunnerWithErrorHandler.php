<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeRunner;

use ErrorException;
use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Throwable;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class CodeRunnerWithErrorHandler implements CodeRunnerInterface
{
    private CodeRunnerInterface $codeExecutor;
    private EventDispatcherInterface $eventDispatcher;
    private string $errorEventName;

    public function __construct(
        CodeRunnerInterface $codeExecutor,
        EventDispatcherInterface $eventDispatcher,
        string $errorEventName
    ) {
        $this->codeExecutor = $codeExecutor;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorEventName = $errorEventName;
    }

    public function runCode(string $code, array $arguments = []): void
    {
        $errorDetails = [
            'arguments' => $arguments,
            'code' => $code,
        ];

        try {
            // Convert everything, including PHP Warnings into an Exception.
            // In this way we process Warnings by our eventDispatcher, rather than having the global PHP Warning.
            set_error_handler(function ($severity, $message, $file, $line) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            });

            $this->codeExecutor->runCode($code, $arguments);
        } catch (Throwable $error) {
            $errorDetails['error'] = $error;

            $this->eventDispatcher->dispatchEvent($this->errorEventName, $errorDetails);
        } finally {
            restore_error_handler();
        }
    }
}
