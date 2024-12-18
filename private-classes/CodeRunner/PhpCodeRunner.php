<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeRunner;

use Prosopo\Views\Interfaces\CodeRunnerInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PhpCodeRunner implements CodeRunnerInterface
{
    public function runCode(string $code, array $arguments = []): void
    {
        // @phpcs:ignore
        extract($arguments);

        // @phpcs:ignore
        eval('?>' . $code);
    }
}
