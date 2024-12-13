<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

use Prosopo\Views\Interfaces\View\ViewInterface;

interface TemplateProviderInterface
{
    public function getTemplate(ViewInterface $view): string;
}
