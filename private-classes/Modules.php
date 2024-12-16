<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Object\PropertyValueProviderForModels;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class Modules implements ModulesInterface
{
    // Required modules:

    private TemplateRendererInterface $templateRenderer;

    //// Custom modules: define them only when you need to override the default behavior:

    private ?ModelFactoryInterface $viewFactory;
    private ?ModelTemplateProviderInterface $modelTemplateProvider;
    private ?ObjectReaderInterface $objectReader;
    private ?ObjectPropertyWriterInterface $objectPropertyWriter;
    private ?PropertyValueProviderInterface $propertyValueProvider;
    private ?ModelRendererInterface $viewRenderer;
    private ?EventDispatcherInterface $eventDispatcher;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
        $this->viewFactory = null;
        $this->modelTemplateProvider = null;
        $this->objectReader = null;
        $this->objectPropertyWriter = null;
        $this->propertyValueProvider = null;
        $this->viewRenderer = null;
        $this->eventDispatcher = null;
    }

    //// Getters.

    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getModelFactory(): ?ModelFactoryInterface
    {
        return $this->viewFactory;
    }

    public function getModelTemplateProvider(): ?ModelTemplateProviderInterface
    {
        return $this->modelTemplateProvider;
    }

    public function getObjectReader(): ?ObjectReaderInterface
    {
        return $this->objectReader;
    }

    public function getObjectPropertyWriter(): ?ObjectPropertyWriterInterface
    {
        return $this->objectPropertyWriter;
    }

    public function getPropertyValueProvider(): ?PropertyValueProviderInterface
    {
        return $this->propertyValueProvider;
    }

    public function getModelRenderer(): ?ModelRendererInterface
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

    public function setModelFactory(?ModelFactoryInterface $viewFactory): self
    {
        $this->viewFactory = $viewFactory;

        return $this;
    }

    public function setModelTemplateProvider(?ModelTemplateProviderInterface $modelTemplateProvider): self
    {
        $this->modelTemplateProvider = $modelTemplateProvider;

        return $this;
    }

    public function setObjectReader(?ObjectReaderInterface $objectPropertyReader): self
    {
        $this->objectReader = $objectPropertyReader;

        return $this;
    }

    public function setObjectPropertyWriter(?ObjectPropertyWriterInterface $objectPropertyWriter): self
    {
        $this->objectPropertyWriter = $objectPropertyWriter;

        return $this;
    }

    public function setPropertyValueProvider(?PropertyValueProviderInterface $propertyValueProvider): self
    {
        $this->propertyValueProvider = $propertyValueProvider;

        return $this;
    }

    public function setModelRenderer(?ModelRendererInterface $viewRenderer): self
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
