<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeExecutor;

use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Throwable;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class CodeExecutorWithErrorEvent implements CodeExecutorInterface
{
    private CodeExecutorInterface $codeExecutor;
    private EventDispatcherInterface $eventDispatcher;
    private string $errorEventName;

    public function __construct(
        CodeExecutorInterface $codeExecutor,
        EventDispatcherInterface $eventDispatcher,
        string $errorEventName
    ) {
        $this->codeExecutor = $codeExecutor;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorEventName = $errorEventName;
    }

    public function executeCode(string $code, array $arguments): void
    {
        $errorDetails = [
            'arguments' => $arguments,
            'code' => $code,
        ];

        try {
            $this->codeExecutor->executeCode($code, $arguments);
        } catch (Throwable $error) {
            $errorDetails['error'] = $error;

            $this->eventDispatcher->dispatchEvent($this->errorEventName, $errorDetails);
        }
    }
}
