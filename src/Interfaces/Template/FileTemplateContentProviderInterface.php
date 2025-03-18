<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Template;

interface FileTemplateContentProviderInterface
{
    public function getFileTemplateContent(string $file): string;
}
