<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyWriterInterface;
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
final class Modules
{
    // Required modules:

    private TemplateRendererInterface $templateRenderer;

    //// Custom modules: define them only when you need to override the default behavior:

    private ?ViewFactoryInterface $viewFactory;
    private ?ViewFactoryInterface $viewFactoryWithPropertyInitialization;
    private ?TemplateProviderInterface $templateProvider;
    private ?ObjectPropertyReaderInterface $objectPropertyReader;
    private ?ObjectPropertyWriterInterface $objectPropertyWriter;
    private ?ObjectPropertyReaderInterface $objectPropertyReaderWithRendering;
    private ?PropertyValueProviderInterface $propertyValueProvider;
    private ?PropertyValueProviderInterface $instancePropertyProvider;
    private ?ViewRendererInterface $viewRenderer;

    public function __construct()
    {
        // Defaults are not set for required modules.
        // This is intentional to ensure an Exception is thrown if their getters are called without providing values.

        $this->viewFactory = null;
        $this->viewFactoryWithPropertyInitialization = null;
        $this->templateProvider = null;
        $this->objectPropertyReader = null;
        $this->objectPropertyWriter = null;
        $this->objectPropertyReaderWithRendering = null;
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

    public function getViewFactoryWithPropertyInitialization(): ?ViewFactoryInterface
    {
        return $this->viewFactoryWithPropertyInitialization;
    }

    public function getTemplateProvider(): ?TemplateProviderInterface
    {
        return $this->templateProvider;
    }

    public function getObjectPropertyReader(): ?ObjectPropertyReaderInterface
    {
        return $this->objectPropertyReader;
    }

    public function getObjectPropertyWriter(): ?ObjectPropertyWriterInterface
    {
        return $this->objectPropertyWriter;
    }

    public function getObjectPropertyReaderWithRendering(): ?ObjectPropertyReaderInterface
    {
        return $this->objectPropertyReaderWithRendering;
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

    public function setTemplateRenderer(TemplateRendererInterface $templateRenderer): self
    {
        $this->templateRenderer = $templateRenderer;

        return $this;
    }

    public function setViewFactory(?ViewFactoryInterface $viewFactory): self
    {
        $this->viewFactory = $viewFactory;

        return $this;
    }

    public function setViewFactoryWithPropertyInitialization(
        ?ViewFactoryInterface $viewFactoryWithPropertyInitialization
    ): self {
        $this->viewFactoryWithPropertyInitialization = $viewFactoryWithPropertyInitialization;

        return $this;
    }

    public function setTemplateProvider(?TemplateProviderInterface $templateProvider): self
    {
        $this->templateProvider = $templateProvider;

        return $this;
    }

    public function setObjectPropertyReader(?ObjectPropertyReaderInterface $objectPropertyReader): self
    {
        $this->objectPropertyReader = $objectPropertyReader;

        return $this;
    }

    public function setObjectPropertyWriter(?ObjectPropertyWriterInterface $objectPropertyWriter): self
    {
        $this->objectPropertyWriter = $objectPropertyWriter;

        return $this;
    }

    public function setObjectPropertyReaderWithRendering(
        ?ObjectPropertyReaderInterface $objectPropertyReaderWithRendering
    ): self {
        $this->objectPropertyReaderWithRendering = $objectPropertyReaderWithRendering;

        return $this;
    }

    public function setPropertyValueProvider(?PropertyValueProviderInterface $propertyValueProvider): self
    {
        $this->propertyValueProvider = $propertyValueProvider;

        return $this;
    }

    public function setInstancePropertyProvider(?PropertyValueProviderInterface $instancePropertyProvider): self
    {
        $this->instancePropertyProvider = $instancePropertyProvider;

        return $this;
    }

    public function setViewRenderer(?ViewRendererInterface $viewRenderer): self
    {
        $this->viewRenderer = $viewRenderer;

        return $this;
    }
}
