<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class TestTemplateModel implements TemplateModelInterface
{
    /**
     * @var array<string,mixed>
     */
    private array $arguments;

    /**
     * @param array<string,mixed> $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function getTemplateArguments(): array
    {
        return $this->arguments;
    }
}
