<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRenderer implements TemplateRendererInterface
{
    private CodeRunnerInterface $codeExecutor;

    public function __construct(CodeRunnerInterface $codeExecutor)
    {
        $this->codeExecutor = $codeExecutor;
    }

    public function renderTemplate(string $template, array $variables = []): string
    {
        ob_start();

        $this->codeExecutor->runCode($template, $variables);

        return (string) ob_get_clean();
    }
}
