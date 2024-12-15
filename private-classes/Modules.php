<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\InstancePropertyProvider;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class Modules implements ModulesInterface
{
    // Required modules:

    private TemplateRendererInterface $templateRenderer;

    //// Custom modules: define them only when you need to override the default behavior:

    private ?ViewFactoryInterface $viewFactory;
    private ?TemplateProviderInterface $templateProvider;
    private ?ObjectPropertyReaderInterface $objectPropertyReader;
    private ?ObjectPropertyWriterInterface $objectPropertyWriter;
    private ?InstancePropertyProvider $instancePropertyProvider;
    private ?PropertyValueProviderInterface $propertyValueProvider;
    private ?ViewRendererInterface $viewRenderer;
    private ?EventDispatcherInterface $eventDispatcher;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
        $this->viewFactory = null;
        $this->templateProvider = null;
        $this->objectPropertyReader = null;
        $this->objectPropertyWriter = null;
        $this->instancePropertyProvider = null;
        $this->propertyValueProvider = null;
        $this->viewRenderer = null;
        $this->eventDispatcher = null;
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

    public function getInstancePropertyProvider(): ?InstancePropertyProvider
    {
        return $this->instancePropertyProvider;
    }

    public function getPropertyValueProvider(): ?PropertyValueProviderInterface
    {
        return $this->propertyValueProvider;
    }

    public function getViewRenderer(): ?ViewRendererInterface
    {
        return $this->viewRenderer;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
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

    public function setInstancePropertyProvider(?InstancePropertyProvider $instancePropertyProvider): self
    {
        $this->instancePropertyProvider = $instancePropertyProvider;

        return $this;
    }

    public function setPropertyValueProvider(?PropertyValueProviderInterface $propertyValueProvider): self
    {
        $this->propertyValueProvider = $propertyValueProvider;

        return $this;
    }

    public function setViewRenderer(?ViewRendererInterface $viewRenderer): self
    {
        $this->viewRenderer = $viewRenderer;

        return $this;
    }

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }
}
