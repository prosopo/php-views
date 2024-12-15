<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

interface TemplateCompilerInterface
{
    public function compileTemplate(string $template): string;
}
