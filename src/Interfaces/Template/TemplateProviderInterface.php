<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;

interface TemplateProviderInterface
{
    public function getTemplate(TemplateModelInterface $model): string;
}
