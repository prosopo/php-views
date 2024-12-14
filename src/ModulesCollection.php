<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, and introduce new public ones.
 *
 * We opt to use a class instead of an interface because it allows for the addition of new (optional) settings,
 * without breaking existing setups.
 */
final class ModulesCollection
{
    // Required modules:

    private TemplateRendererInterface $templateRenderer;

    //// Custom modules: define them only when you need to override the default behavior:

    private ?ViewFactoryInterface $viewFactory;
    private ?ViewFactoryInterface $viewFactoryWithDefaultsSetup;
    private ?TemplateProviderInterface $templateProvider;
    private ?ObjectPropertyManagerInterface $objectPropertyManager;
    private ?PropertyValueProviderInterface $propertyValueProvider;
    private ?PropertyValueProviderInterface $instancePropertyProvider;
    private ?ViewRendererInterface $viewRenderer;

    public function __construct()
    {
        // Defaults are not set for required modules.
        // This is intentional to ensure an Exception is thrown if their getters are called without providing values.

        $this->viewFactory = null;
        $this->viewFactoryWithDefaultsSetup = null;
        $this->templateProvider = null;
        $this->objectPropertyManager = null;
        $this->propertyValueProvider = null;
        $this->instancePropertyProvider = null;
        $this->viewRenderer = null;
    }

    //// Getters.

    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getViewFactory(): ?ViewFactoryInterface
    {
        return $this->viewFactory;
    }

    public function getViewFactoryWithDefaultsSetup(): ?ViewFactoryInterface
    {
        return $this->viewFactoryWithDefaultsSetup;
    }

    public function getTemplateProvider(): ?TemplateProviderInterface
    {
        return $this->templateProvider;
    }

    public function getObjectPropertyManager(): ?ObjectPropertyManagerInterface
    {
        return $this->objectPropertyManager;
    }

    public function getPropertyValueProvider(): ?PropertyValueProviderInterface
    {
        return $this->propertyValueProvider;
    }

    public function getInstancePropertyProvider(): ?PropertyValueProviderInterface
    {
        return $this->instancePropertyProvider;
    }

    public function getViewRenderer(): ?ViewRendererInterface
    {
        return $this->viewRenderer;
    }

    //// Setters.

    public function setTemplateRenderer(TemplateRendererInterface $templateRenderer): void
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function setViewFactory(?ViewFactoryInterface $viewFactory): void
    {
        $this->viewFactory = $viewFactory;
    }

    public function setViewFactoryWithDefaultsSetup(?ViewFactoryInterface $viewFactoryWithDefaultsSetup): void
    {
        $this->viewFactoryWithDefaultsSetup = $viewFactoryWithDefaultsSetup;
    }

    public function setTemplateProvider(?TemplateProviderInterface $templateProvider): void
    {
        $this->templateProvider = $templateProvider;
    }

    public function setObjectPropertyManager(?ObjectPropertyManagerInterface $objectPropertyManager): void
    {
        $this->objectPropertyManager = $objectPropertyManager;
    }

    public function setPropertyValueProvider(?PropertyValueProviderInterface $propertyValueProvider): void
    {
        $this->propertyValueProvider = $propertyValueProvider;
    }

    public function setInstancePropertyProvider(?PropertyValueProviderInterface $instancePropertyProvider): void
    {
        $this->instancePropertyProvider = $instancePropertyProvider;
    }

    public function setViewRenderer(?ViewRendererInterface $viewRenderer): void
    {
        $this->viewRenderer = $viewRenderer;
    }
}
