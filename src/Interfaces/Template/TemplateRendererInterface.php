<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

interface TemplateRendererInterface
{
    /**
     * @param array<string,mixed> $variables
     */
    public function renderTemplate(string $template, array $variables = []): string;
}
