<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

interface TemplateModelInterface
{
    /**
     * @return array<string,mixed>
     */
    public function getTemplateArguments(): array;
}
