<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

class View implements ViewInterface
{
    private TemplateProviderInterface $templateProvider;

    /**
     * The constructor is marked as final to prevent accidental argument overrides.
     * This is essential for the ViewFactory, which automatically creates instances.
     *
     * To set custom default values, use the `setCustomDefaults()` method.
     *
     * Note: If your app Models require additional dependencies in the constructor, consider:
     * - Overriding the ViewFactory module.
     * - Creating a custom parent View class that implements ViewInterface without a final constructor.
     */
    final public function __construct(TemplateProviderInterface $template_provider)
    {
        $this->templateProvider = $template_provider;

        $this->setCustomDefaults();
    }

    public function getTemplate(): string
    {
        return $this->templateProvider->getTemplate($this);
    }

    protected function setCustomDefaults(): void
    {
    }
}
