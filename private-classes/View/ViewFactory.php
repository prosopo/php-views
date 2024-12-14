<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewFactory implements ViewFactoryInterface
{
    private TemplateProviderInterface $templateProvider;

    public function __construct(TemplateProviderInterface $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    public function makeView(string $viewClass): ViewInterface
    {
        return new $viewClass($this->templateProvider);
    }
}
