<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\CodeRunner;

use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class CodeRunnerWithTemplateCompilation implements CodeRunnerInterface
{
    private CodeRunnerInterface $codeExecutor;
    private TemplateCompilerInterface $templateCompiler;

    public function __construct(CodeRunnerInterface $codeExecutor, TemplateCompilerInterface $templateCompiler)
    {
        $this->codeExecutor = $codeExecutor;
        $this->templateCompiler = $templateCompiler;
    }

    public function runCode(string $code, array $arguments = []): void
    {
        $compiledCode = $this->templateCompiler->compileTemplate($code);

        $this->codeExecutor->runCode($compiledCode, $arguments);
    }
}
