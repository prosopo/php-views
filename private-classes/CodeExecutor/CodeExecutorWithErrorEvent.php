<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeExecutor;

use Error;
use Exception;
use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;

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
        $errorContext = [
            'arguments' => $arguments,
            'code' => $code,
        ];

        try {
            // Catch all level-errors and turn into the generic error.
            // @phpcs:ignore
            set_error_handler(
                function ($errno, $errstr) {
                    // @phpcs:ignore
                    throw new Error($errstr, $errno);
                }
            );

            $this->codeExecutor->executeCode($code, $arguments);
        } catch (Error $error) {
            $this->eventDispatcher->dispatchEvent($this->errorEventName, array_merge($errorContext, [
                'error' => $error,
            ]));
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatchEvent($this->errorEventName, array_merge($errorContext, [
                'exception' => $exception,
            ]));
        } finally {
            restore_error_handler();
        }
    }
}
