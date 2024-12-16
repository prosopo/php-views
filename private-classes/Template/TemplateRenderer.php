<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRenderer implements TemplateRendererInterface
{
    private CodeExecutorInterface $codeExecutor;

    public function __construct(CodeExecutorInterface $codeExecutor)
    {
        $this->codeExecutor = $codeExecutor;
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        ob_start();

        $this->codeExecutor->executeCode($template, $variables);

        $html = (string)ob_get_clean();

        if (true === $doPrint) {
            // @phpcs:ignore
            echo $html;
        }

        return $html;
    }
}
