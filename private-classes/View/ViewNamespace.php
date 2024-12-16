<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\Config\ViewNamespaceConfigInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Views\ViewNamespaceInterface;
use Prosopo\Views\PrivateClasses\Object\{ObjectClassReader,
    PropertyValueProviderForModels,
    ObjectReader,
    ObjectReaderWithRendering,
    ObjectPropertyWriter,
    PropertyValueProvider,
    PropertyValueProviderByTypes};
use Prosopo\Views\PrivateClasses\Model\{ModelFactory,
    ModelFactoryWithPropertyInitialization,
    ModelNameProvider,
    ModelNamespaceProvider,
    ModelRenderer,
    ModelRendererWithEventDetails};
use Prosopo\Views\PrivateClasses\EventDispatcher;
use Prosopo\Views\PrivateClasses\Template\FileModelTemplateProvider;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewNamespace implements ViewNamespaceInterface
{
    private ViewNamespaceConfigInterface $config;

    /**
     * Using the external ViewFactory and ViewRenderer enables us to seamlessly mix Models from different namespaces,
     * even if they use different template engines, such as Blade and Twig.
     * (see the Views class)
     */
    public function __construct(
        ViewNamespaceConfigInterface $config,
        ModelFactoryInterface $modelFactoryWithNamespaces,
        ModelRendererInterface $modelRendererWithNamespace
    ) {
        $this->config = clone $config;
        $modules = clone $this->config->getModules();
        $this->config->setModules($modules);

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

        $objectReader = $modules->getObjectReader();
        $objectReader = null === $objectReader ?
            new ObjectReader() :
            $objectReader;

        $objectReader = new ObjectReaderWithRendering(
            $objectReader,
            $modelRendererWithNamespace
        );

        $objectPropertyWriter = $modules->getObjectPropertyWriter();
        $objectPropertyWriter = null === $objectPropertyWriter ?
            new ObjectPropertyWriter() :
            $objectPropertyWriter;

        $modelNamespaceProvider = $modules->getModelNamespaceProvider();
        $modelNamespaceProvider = null === $modelNamespaceProvider ?
            new ModelNamespaceProvider(new ObjectClassReader()) :
            $modelNamespaceProvider;

        $modelNameProvider = $modules->getModelNameProvider();
        $modelNameProvider = null === $modelNameProvider ?
            new ModelNameProvider(new ObjectClassReader()) :
            $modelNameProvider;

        $templateProvider = $modules->getModelTemplateProvider();
        $templateProvider = null === $templateProvider ?
            new FileModelTemplateProvider(
                $config->getTemplatesRootPath(),
                $config->getModelsRootNamespace(),
                $config->getTemplateFileExtension(),
                $modelNamespaceProvider,
                $modelNameProvider
            ) :
            $templateProvider;

        $propertyValueProvider = $modules->getPropertyValueProvider();
        $propertyValueProvider = null === $propertyValueProvider ?
            new PropertyValueProvider() :
            $propertyValueProvider;

        $propertyValueProvider = new PropertyValueProviderByTypes(
            $propertyValueProvider,
            $config->getDefaultPropertyValues()
        );

        $propertyValueProvider = new PropertyValueProviderForModels(
            $propertyValueProvider,
            $modelFactoryWithNamespaces
        );

        $modelFactory = new ModelFactoryWithPropertyInitialization(
            $modelFactoryWithNamespaces,
            // Plain reader, without rendering.
            $objectReader,
            $objectPropertyWriter,
            $propertyValueProvider
        );

        //// 2. Real Factory and Renderer creation (used in the Views class):

        $realViewFactory = $modules->getModelFactory();
        $realViewFactory = null === $realViewFactory ?
            new ModelFactory($objectReader) :
            $realViewFactory;

        $realViewRenderer = $modules->getModelRenderer();
        $realViewRenderer = null === $realViewRenderer ?
            new ModelRenderer(
                $modules->getTemplateRenderer(),
                $modelFactory,
                $objectReader,
                $templateProvider
            ) :
            $realViewRenderer;

        $realViewRenderer = new ModelRendererWithEventDetails(
            $realViewRenderer,
            $eventDispatcher,
            $templateErrorEventName
        );

        //// 3. Now we can save the objects to the storage.

        $modules->setEventDispatcher($eventDispatcher)
                ->setObjectReader($objectReader)
                ->setObjectPropertyWriter($objectPropertyWriter)
                ->setModelTemplateProvider($templateProvider)
                ->setPropertyValueProvider($propertyValueProvider)
                ->setModelFactory($realViewFactory)
                ->setModelRenderer($realViewRenderer)
                ->setModelNamespaceProvider($modelNamespaceProvider)
                ->setModelNameProvider($modelNameProvider);
    }

    public function getConfig(): ViewNamespaceConfigInterface
    {
        return $this->config;
    }
}