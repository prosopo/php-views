<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeExecutor;

use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class CodeExecutorWithTemplateCompilation implements CodeExecutorInterface
{
    private CodeExecutorInterface $codeExecutor;
    private TemplateCompilerInterface $templateCompiler;

    public function __construct(CodeExecutorInterface $codeExecutor, TemplateCompilerInterface $templateCompiler)
    {
        $this->codeExecutor = $codeExecutor;
        $this->templateCompiler = $templateCompiler;
    }

    public function executeCode(string $code, array $arguments = []): void
    {
        $code = $this->templateCompiler->compileTemplate($code);

        $this->codeExecutor->executeCode($code, $arguments);
    }
}
