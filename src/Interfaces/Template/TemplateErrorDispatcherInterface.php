<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

use Error;
use Exception;

interface TemplateErrorDispatcherInterface
{
    /**
     * @param Error|Exception $errorOrException
     * @param array<string,mixed> $variables
     */
    public function dispatch($errorOrException, string $template, string $compiledPhpTemplate, array $variables): void;
}
