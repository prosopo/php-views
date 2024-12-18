<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNameResolverInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceResolverInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties, and introduce new public ones.
 */
final class ViewNamespaceModules
{
    // Required modules:

    private TemplateRendererInterface $templateRenderer;

    //// Custom modules: define them only when you need to override the default behavior:

    private ?ModelFactoryInterface $modelFactory;
    private ?ModelTemplateResolverInterface $modelTemplateResolver;
    private ?ObjectReaderInterface $objectReader;
    private ?ObjectPropertyWriterInterface $objectPropertyWriter;
    private ?PropertyValueProviderInterface $propertyValueProvider;
    private ?ModelRendererInterface $modelRenderer;
    private ?EventDispatcherInterface $eventDispatcher;
    private ?ModelNameResolverInterface $modelNameResolver;
    private ?ModelNamespaceResolverInterface $modelNamespaceResolver;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
        $this->modelFactory = null;
        $this->modelTemplateResolver = null;
        $this->objectReader = null;
        $this->objectPropertyWriter = null;
        $this->propertyValueProvider = null;
        $this->modelRenderer = null;
        $this->eventDispatcher = null;
        $this->modelNameResolver = null;
        $this->modelNamespaceResolver = null;
    }

    //// Getters.

    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getModelFactory(): ?ModelFactoryInterface
    {
        return $this->modelFactory;
    }

    public function getModelTemplateResolver(): ?ModelTemplateResolverInterface
    {
        return $this->modelTemplateResolver;
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
        return $this->modelRenderer;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getModelNameResolver(): ?ModelNameResolverInterface
    {
        return $this->modelNameResolver;
    }

    public function getModelNamespaceResolver(): ?ModelNamespaceResolverInterface
    {
        return $this->modelNamespaceResolver;
    }

    //// Setters.

    public function setTemplateRenderer(TemplateRendererInterface $templateRenderer): self
    {
        $this->templateRenderer = $templateRenderer;

        return $this;
    }

    public function setModelFactory(?ModelFactoryInterface $viewFactory): self
    {
        $this->modelFactory = $viewFactory;

        return $this;
    }

    public function setModelTemplateResolver(?ModelTemplateResolverInterface $modelTemplateResolver): self
    {
        $this->modelTemplateResolver = $modelTemplateResolver;

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
        $this->modelRenderer = $viewRenderer;

        return $this;
    }

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    public function setModelNameResolver(?ModelNameResolverInterface $modelNameResolver): self
    {
        $this->modelNameResolver = $modelNameResolver;

        return $this;
    }

    public function setModelNamespaceResolver(?ModelNamespaceResolverInterface $modelNamespaceResolver): self
    {
        $this->modelNamespaceResolver = $modelNamespaceResolver;

        return $this;
    }
}
