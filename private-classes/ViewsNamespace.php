<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses;

use Prosopo\Views\Interfaces\Config\NamespaceConfigInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\Interfaces\Views\ViewsNamespaceInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\{InstancePropertyProvider,
    ObjectPropertyReader,
    ObjectPropertyReaderWithRendering,
    ObjectPropertyWriter,
    PropertyValueProvider};
use Prosopo\Views\PrivateClasses\Template\TemplateProvider;
use Prosopo\Views\PrivateClasses\View\{ViewFactory,
    ViewFactoryWithPropertyInitialization,
    ViewRenderer,
    ViewRendererWithEventDetails};

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewsNamespace implements ViewsNamespaceInterface
{
    private ModulesInterface $modules;

    /**
     * Using the external ViewFactory and ViewRenderer enables us to seamlessly mix Models from different namespaces,
     * even if they use different template engines, such as Blade and Twig.
     * (see the Views class)
     */
    public function __construct(
        NamespaceConfigInterface $config,
        ViewFactoryInterface $viewFactoryWithNamespaces,
        ViewRendererInterface $viewRendererWithNamespaces
    ) {
        $modules = clone $config->getModules();

        //// 1. Modules creation:

        $templateErrorEventName = $config->getTemplateErrorEventName();

        $eventDispatcher = $modules->getEventDispatcher();
        $eventDispatcher = null === $eventDispatcher ?
            new EventDispatcher() :
            $eventDispatcher;

        $templateErrorHandler = $config->getTemplateErrorHandler();
        if (null !== $templateErrorHandler) {
            $eventDispatcher->addEventListener($templateErrorEventName, $templateErrorHandler);
        }

        $objectPropertyReader = $modules->getObjectPropertyReader();
        $objectPropertyReader = null === $objectPropertyReader ?
            new ObjectPropertyReader() :
            $objectPropertyReader;

        $objectPropertyReader = new ObjectPropertyReaderWithRendering(
            $objectPropertyReader,
            $viewRendererWithNamespaces
        );

        $objectPropertyWriter = $modules->getObjectPropertyWriter();
        $objectPropertyWriter = null === $objectPropertyWriter ?
            new ObjectPropertyWriter() :
            $objectPropertyWriter;

        $templateProvider = $modules->getTemplateProvider();
        $templateProvider = null === $templateProvider ?
            new TemplateProvider(
                $config->getTemplatesRootPath(),
                $config->getViewsRootNamespace(),
                $config->getTemplateFileExtension()
            ) :
            $templateProvider;

        $instancePropertyProvider = $modules->getInstancePropertyProvider();
        $instancePropertyProvider = null === $instancePropertyProvider ?
            new InstancePropertyProvider($viewFactoryWithNamespaces) :
            $instancePropertyProvider;

        $propertyValueProvider = $modules->getPropertyValueProvider();
        $propertyValueProvider = null === $propertyValueProvider ?
            new PropertyValueProvider($instancePropertyProvider, $config->getDefaultPropertyValues()) :
            $propertyValueProvider;

        $viewFactory = new ViewFactoryWithPropertyInitialization(
            $viewFactoryWithNamespaces,
            // Plain reader, without rendering.
            $objectPropertyReader,
            $objectPropertyWriter,
            $propertyValueProvider
        );

        //// 2. Real Factory and Renderer creation (used in the Views class):

        $realViewFactory = $modules->getViewFactory();
        $realViewFactory = null === $realViewFactory ?
            new ViewFactory($templateProvider) :
            $realViewFactory;

        $realViewRenderer = $modules->getViewRenderer();
        $realViewRenderer = null === $realViewRenderer ?
            new ViewRenderer(
                $modules->getTemplateRenderer(),
                $viewFactory,
                $objectPropertyReader
            ) :
            $realViewRenderer;

        $realViewRenderer = new ViewRendererWithEventDetails(
            $realViewRenderer,
            $eventDispatcher,
            $templateErrorEventName
        );

        //// 3. Now we can save the objects to the storage.

        $modules->setObjectPropertyReader($objectPropertyReader)
                ->setObjectPropertyWriter($objectPropertyWriter)
                ->setTemplateProvider($templateProvider)
                ->setInstancePropertyProvider($instancePropertyProvider)
                ->setPropertyValueProvider($propertyValueProvider)
                ->setViewFactory($realViewFactory)
                ->setViewRenderer($realViewRenderer);

        $this->modules = $modules;
    }

    public function getModules(): ModulesInterface
    {
        return $this->modules;
    }
}
