<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Modules;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNameProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

interface ModulesInterface
{
    //// Getters.

    public function getTemplateRenderer(): TemplateRendererInterface;

    public function getModelFactory(): ?ModelFactoryInterface;

    public function getModelTemplateProvider(): ?ModelTemplateProviderInterface;

    public function getObjectReader(): ?ObjectReaderInterface;

    public function getObjectPropertyWriter(): ?ObjectPropertyWriterInterface;

    public function getPropertyValueProvider(): ?PropertyValueProviderInterface;

    public function getModelRenderer(): ?ModelRendererInterface;

    public function getEventDispatcher(): ?EventDispatcherInterface;

    public function getModelNameProvider(): ?ModelNameProviderInterface;

    public function getModelNamespaceProvider(): ?ModelNamespaceProviderInterface;

    //// Setters.

    public function setTemplateRenderer(TemplateRendererInterface $templateRenderer): self;

    public function setModelFactory(?ModelFactoryInterface $viewFactory): self;

    public function setModelTemplateProvider(?ModelTemplateProviderInterface $modelTemplateProvider): self;

    public function setObjectReader(?ObjectReaderInterface $objectPropertyReader): self;

    public function setObjectPropertyWriter(?ObjectPropertyWriterInterface $objectPropertyWriter): self;

    public function setPropertyValueProvider(?PropertyValueProviderInterface $propertyValueProvider): self;

    public function setModelRenderer(?ModelRendererInterface $viewRenderer): self;

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self;

    public function setModelNameProvider(?ModelNameProviderInterface $modelNameProvider): self;

    public function setModelNamespaceProvider(?ModelNamespaceProviderInterface $modelNamespaceProvider): self;
}
