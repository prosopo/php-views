<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\Template\TemplateErrorDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateErrorDispatcher implements TemplateErrorDispatcherInterface
{
    /**
     * @var callable(TemplateErrorInterface $templateError): void|null
     */
    private $errorHandler;

    /**
     * @param callable(TemplateErrorInterface $templateError): void|null $errorHandler
     */
    public function __construct(?callable $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function dispatch($errorOrException, string $template, string $compiledPhpTemplate, array $variables): void
    {
        if (null === $this->errorHandler) {
            return;
        }

        $errorHandler = $this->errorHandler;

        $templateError = new TemplateError(
            $template,
            $compiledPhpTemplate,
            $errorOrException->getMessage(),
            $errorOrException->getLine(),
            $variables
        );

        $errorHandler($templateError);
    }
}
