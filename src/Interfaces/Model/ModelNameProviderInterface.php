<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

interface ModelNameProviderInterface
{
    public function getModelName(TemplateModelInterface $model): string;
}
