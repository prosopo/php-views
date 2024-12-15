<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

abstract class View implements ViewInterface
{
    private TemplateProviderInterface $templateProvider;

    /**
     * The constructor is marked as final to prevent accidental argument overrides.
     * This is essential for the ViewFactory, which automatically creates instances.
     *
     * To set custom default values for primitives, use the setCustomDefaults() method.
     *
     * If your app Models require additional object dependencies, consider one of the following approaches:
     *
     * 1. Override the InstancePropertyProvider module (recommended)
     *
     * This module is responsible for providing default values for instance properties.
     * You can create your own implementation, for example,
     * to integrate with a Dependency Injection container like PHP-DI. This allows class properties to
     * be automatically resolved while object creation by your application's DI system.
     *
     * 2. Override the ViewFactory module (alternative)
     *
     * Alternatively, you can override the ViewFactory to integrate PHP-DI for resolving dependencies.
     * But in this approach, you need also to create a custom parent View class that implements ViewInterface
     * without the final constructor.
     */
    final public function __construct(TemplateProviderInterface $template_provider)
    {
        $this->templateProvider = $template_provider;

        $this->setCustomDefaults();
    }

    public function getTemplate(): string
    {
        return $this->templateProvider->getTemplate(get_class($this));
    }

    protected function setCustomDefaults(): void
    {
    }
}
