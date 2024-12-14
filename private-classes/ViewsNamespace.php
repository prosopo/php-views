<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses;

use Prosopo\Views\Interfaces\ObjectProperty\{ObjectPropertyReaderInterface,
    ObjectPropertyWriterInterface,
    PropertyValueProviderInterface};
use Prosopo\Views\Interfaces\Template\{TemplateProviderInterface, TemplateRendererInterface};
use Prosopo\Views\Interfaces\View\{ViewFactoryInterface, ViewRendererInterface};
use Prosopo\Views\Interfaces\ViewsNamespaceInterface;
use Prosopo\Views\Modules;
use Prosopo\Views\NamespaceConfig;
use Prosopo\Views\PrivateClasses\ObjectProperty\{InstancePropertyProvider,
    ObjectPropertyReader,
    ObjectPropertyReaderWithRendering,
    ObjectPropertyWriter,
    PropertyValueProvider};
use Prosopo\Views\PrivateClasses\Template\TemplateProvider;
use Prosopo\Views\PrivateClasses\View\{ViewFactory, ViewFactoryWithPropertyInitialization, ViewRenderer};

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewsNamespace implements ViewsNamespaceInterface
{
    private Modules $modules;

    /**
     * Using the external ViewFactory and ViewRenderer enables us to seamlessly mix Models from different namespaces,
     * even if they use different template engines, such as Blade and Twig.
     * (see the Views class)
     */
    public function __construct(
        NamespaceConfig $config,
        ViewFactoryInterface $viewFactoryWithNamespaces,
        ViewRendererInterface $viewRendererWithNamespaces
    ) {
        $modules = clone $config->getModules();

        //// 1. Modules creation:

        $objectPropertyReader = $modules->getObjectPropertyReader();
        $objectPropertyReader = null === $objectPropertyReader ?
            $this->makeObjectPropertyReader() :
            $objectPropertyReader;

        $objectPropertyReaderWithRendering = $modules->getObjectPropertyReaderWithRendering();
        $objectPropertyReaderWithRendering = null === $objectPropertyReaderWithRendering ?
            $this->makeObjectPropertyReaderWithRendering($objectPropertyReader, $viewRendererWithNamespaces) :
            $objectPropertyReaderWithRendering;

        $objectPropertyWriter = $modules->getObjectPropertyWriter();
        $objectPropertyWriter = null === $objectPropertyWriter ?
            $this->makeObjectPropertyWriter() :
            $objectPropertyWriter;

        $templateProvider = $modules->getTemplateProvider();
        $templateProvider = null === $templateProvider ?
            $this->makeTemplateProvider(
                $config->getTemplatesRootPath(),
                $config->getViewsRootNamespace(),
                $config->getTemplateFileExtension()
            ) :
            $templateProvider;

        $instancePropertyProvider = $modules->getInstancePropertyProvider();
        $instancePropertyProvider = null === $instancePropertyProvider ?
            $this->makeInstancePropertyProvider($viewFactoryWithNamespaces) :
            $instancePropertyProvider;

        $propertyValueProvider = $modules->getPropertyValueProvider();
        $propertyValueProvider = null === $propertyValueProvider ?
            $this->makePropertyValueProvider($instancePropertyProvider, $config->getDefaultPropertyValues()) :
            $propertyValueProvider;

        $viewFactoryWithPropertyInitialization = $modules->getViewFactoryWithPropertyInitialization();
        $viewFactoryWithPropertyInitialization = null === $viewFactoryWithPropertyInitialization ?
            $this->makeViewFactoryWithPropertyInitialization(
                $viewFactoryWithNamespaces,
                // Plain reader, without rendering.
                $objectPropertyReader,
                $objectPropertyWriter,
                $propertyValueProvider
            ) :
            $viewFactoryWithPropertyInitialization;

        //// 2. Real Factory and Renderer creation (used in the Views class):

        $viewFactory = $modules->getViewFactory();
        $viewFactory = null === $viewFactory ?
            $this->makeViewFactory($templateProvider) :
            $viewFactory;

        $viewRenderer = $modules->getViewRenderer();
        $viewRenderer = null === $viewRenderer ?
            $this->makeViewRenderer(
                $modules->getTemplateRenderer(),
                $viewFactoryWithPropertyInitialization,
                $objectPropertyReaderWithRendering
            ) :
            $viewRenderer;

        //// 3. Now we can save the objects to the storage.

        $modules->setObjectPropertyReader($objectPropertyReader)
                ->setObjectPropertyReaderWithRendering($objectPropertyReaderWithRendering)
                ->setObjectPropertyWriter($objectPropertyWriter)
                ->setTemplateProvider($templateProvider)
                ->setInstancePropertyProvider($instancePropertyProvider)
                ->setPropertyValueProvider($propertyValueProvider)
                ->setViewFactoryWithPropertyInitialization($viewFactoryWithPropertyInitialization)
                ->setViewFactory($viewFactory)
                ->setViewRenderer($viewRenderer);

        $this->modules = $modules;
    }

    public function getModules(): Modules
    {
        return $this->modules;
    }

    protected function makeInstancePropertyProvider(ViewFactoryInterface $viewFactory): PropertyValueProviderInterface
    {
        return new InstancePropertyProvider($viewFactory);
    }

    protected function makeObjectPropertyReader(): ObjectPropertyReaderInterface
    {
        return new ObjectPropertyReader();
    }

    protected function makeObjectPropertyWriter(): ObjectPropertyWriterInterface
    {
        return new ObjectPropertyWriter();
    }

    protected function makeObjectPropertyReaderWithRendering(
        ObjectPropertyReaderInterface $objectPropertyReader,
        ViewRendererInterface $viewRenderer
    ): ObjectPropertyReaderInterface {
        return new ObjectPropertyReaderWithRendering($objectPropertyReader, $viewRenderer);
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
        ObjectPropertyReaderInterface $objectPropertyReader,
        ObjectPropertyWriterInterface $objectPropertyWriter,
        PropertyValueProviderInterface $propertyValueProvider
    ): ViewFactoryInterface {
        return new ViewFactoryWithPropertyInitialization(
            $viewFactory,
            $objectPropertyReader,
            $objectPropertyWriter,
            $propertyValueProvider
        );
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
        ObjectPropertyReaderInterface $objectPropertyReader
    ): ViewRendererInterface {
        return new ViewRenderer(
            $templateRenderer,
            $viewFactory,
            $objectPropertyReader
        );
    }
}
