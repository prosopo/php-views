<?php

declare(strict_types=1);

namespace Prosopo\Views\Template;

use Prosopo\Views\Interfaces\Template\TemplateErrorDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

class TemplateErrorDispatcher implements TemplateErrorDispatcherInterface
{
    /**
     * @var callable(TemplateErrorInterface $templateError): void|null
     */
    private $errorHandler;

    /**
     * @param callable(TemplateErrorInterface $templateError): void|null $errorHandler
     */
    public function __construct(callable $errorHandler = null)
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
