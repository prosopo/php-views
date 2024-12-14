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
use Prosopo\Views\PrivateClasses\View\ViewFactoryWithDefaultsSetup;
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
        $this->modules = clone $config->getModulesCollection();

        // fixme use $this->modules when it needs.
        $objectPropertyManager = $this->getOrMakeObjectPropertyManager($config);

        $this->viewFactory = $this->getOrMakeViewFactoryWithDefaultsSetup($config, $objectPropertyManager);
        $this->viewRenderer = $this->getOrMakeViewRenderer($config, $this->viewFactory, $objectPropertyManager);
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

    //// Conditional instance retrievals:

    protected function getOrMakeViewFactoryWithDefaultsSetup(
        ViewsConfig $config,
        ObjectPropertyManagerInterface $objectPropertyManager
    ): ViewFactoryInterface {
        $viewFactoryWithDefaultsSetup = $config->getViewFactoryWithDefaultsSetup();

        if (null !== $viewFactoryWithDefaultsSetup) {
            return $viewFactoryWithDefaultsSetup;
        }

        $viewFactory = $this->getOrMakeViewFactory($config);
        $instancePropertyProvider = $this->getOrMakeInstancePropertyProvider($config, $viewFactory);
        $propertyValueProvider = $this->getOrMakePropertyValueProvider($config, $instancePropertyProvider);

        return $this->makeViewFactoryWithDefaultsSetup($viewFactory, $objectPropertyManager, $propertyValueProvider);
    }

    protected function getOrMakeViewFactory(
        ViewsConfig $config
    ): ViewFactoryInterface {
        $viewFactory = $config->getViewFactory();

        if (null !== $viewFactory) {
            return $viewFactory;
        }

        $templateProvider = $this->getOrMakeTemplateProvider($config);

        return $this->makeViewFactory($templateProvider);
    }

    protected function getOrMakeObjectPropertyManager(ViewsConfig $config): ObjectPropertyManagerInterface
    {
        if (null !== $config->getObjectPropertyManager()) {
            return $config->getObjectPropertyManager();
        }

        return $this->makeObjectPropertyManager();
    }

    protected function getOrMakePropertyValueProvider(
        ViewsConfig $config,
        PropertyValueProviderInterface $instancePropertyProvider
    ): PropertyValueProviderInterface {
        $propertyValueProvider = $config->getPropertyValueProvider();

        if (null !== $propertyValueProvider) {
            return $propertyValueProvider;
        }

        return $this->makePropertyValueProvider($instancePropertyProvider, $config->getDefaultPropertyValues());
    }

    protected function getOrMakeTemplateProvider(ViewsConfig $config): TemplateProviderInterface
    {
        $templateProvider = $config->getTemplateProvider();

        if (null !== $templateProvider) {
            return $templateProvider;
        }

        return $this->makeTemplateProvider(
            $config->getTemplatesRootPath(),
            $config->getViewsRootNamespace(),
            $config->getTemplateFileExtension()
        );
    }

    protected function getOrMakeViewRenderer(
        ViewsConfig $config,
        ViewFactoryInterface $viewFactory,
        ObjectPropertyManagerInterface $objectPropertyManager
    ): ViewRendererInterface {
        $viewRenderer = $config->getViewRenderer();

        if (null !== $viewRenderer) {
            return $viewRenderer;
        }

        return $this->makeViewRenderer($config->getTemplateRenderer(), $viewFactory, $objectPropertyManager);
    }

    protected function getOrMakeInstancePropertyProvider(
        ViewsConfig $config,
        ViewFactoryInterface $viewFactory
    ): PropertyValueProviderInterface {
        $instancePropertyProvider = $config->getInstancePropertyProvider();

        if (null !== $instancePropertyProvider) {
            return $instancePropertyProvider;
        }

        return $this->makeInstancePropertyProvider($viewFactory);
    }

    //// Default instance creators:

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

    protected function makeViewFactoryWithDefaultsSetup(
        ViewFactoryInterface $viewFactory,
        ObjectPropertyManagerInterface $objectPropertyManager,
        PropertyValueProviderInterface $propertyValueProvider
    ): ViewFactoryInterface {
        return new ViewFactoryWithDefaultsSetup($viewFactory, $objectPropertyManager, $propertyValueProvider);
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
