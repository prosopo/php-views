<?php

declare(strict_types=1);

namespace Prosopo\Views\Template;

use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

class TemplateError implements TemplateErrorInterface
{
    private string $template;
    private string $compiledPhpTemplate;
    private string $message;
    private int $line;
    /**
     * @var array<string,mixed>
     */
    private array $variables;

    /**
     * @param array<string,mixed> $variables
     */
    public function __construct(
        string $template,
        string $compiledPhpTemplate,
        string $message,
        int $line,
        array $variables
    ) {
        $this->template = $template;
        $this->compiledPhpTemplate = $compiledPhpTemplate;
        $this->message = $message;
        $this->line = $line;
        $this->variables = $variables;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getCompiledPhpTemplate(): string
    {
        return $this->compiledPhpTemplate;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
