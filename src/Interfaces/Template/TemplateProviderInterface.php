<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

interface TemplateProviderInterface
{
    public function getTemplate(string $viewClass): string;
}
