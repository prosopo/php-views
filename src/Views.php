<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\Interfaces\ViewsInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\InstancePropertyProvider;
use Prosopo\Views\PrivateClasses\ObjectProperty\ObjectPropertyManager;
use Prosopo\Views\PrivateClasses\ObjectProperty\PropertyValueProvider;
use Prosopo\Views\PrivateClasses\Template\TemplateProvider;
use Prosopo\Views\PrivateClasses\View\ViewFactory;
use Prosopo\Views\PrivateClasses\View\ViewFactoryWithPropertyInitialization;
use Prosopo\Views\PrivateClasses\View\ViewRenderer;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, and introduce new public ones.
 */
final class Views implements ViewsInterface
{
    private ViewRendererInterface $viewRenderer;
    private ViewFactoryInterface $viewFactory;
    private ModulesCollection $modules;

    public function __construct(ViewsConfig $config)
    {
        $modules = clone $config->getModulesCollection();

        $objectPropertyManager = $modules->getObjectPropertyManager();
        $objectPropertyManager = null === $objectPropertyManager ?
            $this->makeObjectPropertyManager() :
            $objectPropertyManager;
        $modules->setObjectPropertyManager($objectPropertyManager);

        $templateProvider = $modules->getTemplateProvider();
        $templateProvider = null === $templateProvider ?
            $this->makeTemplateProvider(
                $config->getTemplatesRootPath(),
                $config->getViewsRootNamespace(),
                $config->getTemplateFileExtension()
            ) :
            $templateProvider;
        $modules->setTemplateProvider($templateProvider);

        $viewFactory = $modules->getViewFactory();
        $viewFactory = null === $viewFactory ?
            $this->makeViewFactory($templateProvider) :
            $viewFactory;
        $modules->setViewFactory($viewFactory);

        $instancePropertyProvider = $modules->getInstancePropertyProvider();
        $instancePropertyProvider = null === $instancePropertyProvider ?
            $this->makeInstancePropertyProvider($viewFactory) :
            $instancePropertyProvider;
        $modules->setInstancePropertyProvider($instancePropertyProvider);

        $propertyValueProvider = $modules->getPropertyValueProvider();
        $propertyValueProvider = null === $propertyValueProvider ?
            $this->makePropertyValueProvider($instancePropertyProvider, $config->getDefaultPropertyValues()) :
            $propertyValueProvider;
        $modules->setPropertyValueProvider($propertyValueProvider);

        $viewFactoryWithPropertyInitialization = $modules->getViewFactoryWithPropertyInitialization();
        $viewFactoryWithPropertyInitialization = null === $viewFactoryWithPropertyInitialization ?
            $this->makeViewFactoryWithPropertyInitialization(
                $viewFactory,
                $objectPropertyManager,
                $propertyValueProvider
            ) :
            $viewFactoryWithPropertyInitialization;
        $modules->setViewFactoryWithPropertyInitialization($viewFactoryWithPropertyInitialization);

        $viewRenderer = $modules->getViewRenderer();
        $viewRenderer = null === $viewRenderer ?
            $this->makeViewRenderer($modules->getTemplateRenderer(), $viewFactory, $objectPropertyManager) :
            $viewRenderer;
        $modules->setViewRenderer($viewRenderer);

        $this->viewFactory = $viewFactoryWithPropertyInitialization;
        $this->viewRenderer = $viewRenderer;
        $this->modules = $modules;
    }

    public function getFactory(): ViewFactoryInterface
    {
        return $this->viewFactory;
    }

    public function getRenderer(): ViewRendererInterface
    {
        return $this->viewRenderer;
    }

    public function getModules(): ModulesCollection
    {
        return $this->modules;
    }

    protected function makeInstancePropertyProvider(ViewFactoryInterface $viewFactory): PropertyValueProviderInterface
    {
        return new InstancePropertyProvider($viewFactory);
    }

    protected function makeObjectPropertyManager(): ObjectPropertyManagerInterface
    {
        return new ObjectPropertyManager();
    }

    /**
     * @param array<string,mixed> $defaultPropertyValues
     */
    protected function makePropertyValueProvider(
        PropertyValueProviderInterface $instancePropertyProvider,
        array $defaultPropertyValues
    ): PropertyValueProviderInterface {
        return new PropertyValueProvider($instancePropertyProvider, $defaultPropertyValues);
    }

    protected function makeViewFactory(TemplateProviderInterface $templateProvider): ViewFactoryInterface
    {
        return new ViewFactory($templateProvider);
    }

    protected function makeViewFactoryWithPropertyInitialization(
        ViewFactoryInterface $viewFactory,
        ObjectPropertyManagerInterface $objectPropertyManager,
        PropertyValueProviderInterface $propertyValueProvider
    ): ViewFactoryInterface {
        return new ViewFactoryWithPropertyInitialization($viewFactory, $objectPropertyManager, $propertyValueProvider);
    }

    protected function makeTemplateProvider(
        string $templatesRootPath,
        string $viewsRootNamespace,
        string $templateFileExtension
    ): TemplateProviderInterface {
        return new TemplateProvider(
            $templatesRootPath,
            $viewsRootNamespace,
            $templateFileExtension
        );
    }

    protected function makeViewRenderer(
        TemplateRendererInterface $templateRenderer,
        ViewFactoryInterface $viewFactory,
        ObjectPropertyManagerInterface $objectPropertyManager
    ): ViewRendererInterface {
        return new ViewRenderer(
            $templateRenderer,
            $viewFactory,
            $objectPropertyManager
        );
    }
}
