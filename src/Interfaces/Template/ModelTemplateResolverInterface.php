<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;

interface ModelTemplateResolverInterface
{
    public function resolveModelTemplate(TemplateModelInterface $model): string;
}
