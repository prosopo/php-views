<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Modules;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\InstancePropertyProvider;

interface ModulesInterface
{
    //// Getters.

    public function getTemplateRenderer(): TemplateRendererInterface;

    public function getViewFactory(): ?ViewFactoryInterface;

    public function getTemplateProvider(): ?TemplateProviderInterface;

    public function getObjectPropertyReader(): ?ObjectPropertyReaderInterface;

    public function getObjectPropertyWriter(): ?ObjectPropertyWriterInterface;

    public function getInstancePropertyProvider(): ?InstancePropertyProvider;

    public function getPropertyValueProvider(): ?PropertyValueProviderInterface;

    public function getViewRenderer(): ?ViewRendererInterface;

    public function getEventDispatcher(): ?EventDispatcherInterface;

    //// Setters.

    public function setTemplateRenderer(TemplateRendererInterface $templateRenderer): self;

    public function setViewFactory(?ViewFactoryInterface $viewFactory): self;

    public function setTemplateProvider(?TemplateProviderInterface $templateProvider): self;

    public function setObjectPropertyReader(?ObjectPropertyReaderInterface $objectPropertyReader): self;

    public function setObjectPropertyWriter(?ObjectPropertyWriterInterface $objectPropertyWriter): self;

    public function setInstancePropertyProvider(?InstancePropertyProvider $instancePropertyProvider): self;

    public function setPropertyValueProvider(?PropertyValueProviderInterface $propertyValueProvider): self;

    public function setViewRenderer(?ViewRendererInterface $viewRenderer): self;

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self;
}
