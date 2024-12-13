<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\Interfaces\ViewsInterface;
use Prosopo\Views\Template\TemplateProvider;
use Prosopo\Views\View\ViewFactory;
use Prosopo\Views\View\ViewRenderer;

class Views implements ViewsInterface
{
    private ViewRendererInterface $renderer;
    private ViewFactoryInterface $factory;

    public function __construct(ViewsConfig $config)
    {
        $objectPropertyManager = $this->getOrMakeObjectPropertyManager($config);
        $this->factory = $this->getOrMakeFactory($objectPropertyManager, $config);
        $this->renderer = $this->getOrMakeViewRenderer($config, $this->factory, $objectPropertyManager);
    }

    public function getFactory(): ViewFactoryInterface
    {
        return $this->factory;
    }

    public function getRenderer(): ViewRendererInterface
    {
        return $this->renderer;
    }

    //// Conditional instance retrievals:

    protected function getOrMakeFactory(
        ObjectPropertyManagerInterface $objectPropertyManager,
        ViewsConfig $config
    ): ViewFactoryInterface {
        $viewFactory = $config->getViewFactory();

        if (null !== $viewFactory) {
            return $viewFactory;
        }

        $templateProvider = $this->getOrMakeTemplateProvider($config);

        return $this->makeFactory($objectPropertyManager, $templateProvider);
    }

    protected function getOrMakeObjectPropertyManager(ViewsConfig $config): ObjectPropertyManagerInterface
    {
        if (null !== $config->getObjectPropertyManager()) {
            return $config->getObjectPropertyManager();
        }

        return $this->makeObjectPropertyManager();
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

    //// Default instance creators:

    protected function makeObjectPropertyManager(): ObjectPropertyManagerInterface
    {
        return new ObjectPropertyManager();
    }

    protected function makeFactory(
        ObjectPropertyManagerInterface $objectPropertyManager,
        TemplateProviderInterface $templateProvider
    ): ViewFactoryInterface {
        return new ViewFactory($objectPropertyManager, $templateProvider);
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
