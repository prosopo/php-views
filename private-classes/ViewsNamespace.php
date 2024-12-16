<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses;

use Prosopo\Views\Interfaces\Config\ViewsNamespaceConfigInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Views\ViewsNamespaceInterface;
use Prosopo\Views\PrivateClasses\Object\{PropertyValueProviderForModels,
    ObjectReader,
    ObjectReaderWithRendering,
    ObjectPropertyWriter,
    PropertyValueProvider,
    PropertyValueProviderByTypes};
use Prosopo\Views\PrivateClasses\Model\{ModelFactory,
    ModelFactoryWithPropertyInitialization,
    ModelRenderer,
    ModelRendererWithEventDetails};
use Prosopo\Views\PrivateClasses\Template\FileModelTemplateProvider;

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
        ViewsNamespaceConfigInterface $config,
        ModelFactoryInterface $modelFactoryWithNamespaces,
        ModelRendererInterface $modelRendererWithNamespace
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

        $templateProvider = $modules->getModelTemplateProvider();
        $templateProvider = null === $templateProvider ?
            new FileModelTemplateProvider(
                $config->getTemplatesRootPath(),
                $config->getModelsRootNamespace(),
                $config->getTemplateFileExtension()
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

        $modules->setObjectReader($objectReader)
                ->setObjectPropertyWriter($objectPropertyWriter)
                ->setModelTemplateProvider($templateProvider)
                ->setPropertyValueProvider($propertyValueProvider)
                ->setModelFactory($realViewFactory)
                ->setModelRenderer($realViewRenderer);

        $this->modules = $modules;
    }

    public function getModules(): ModulesInterface
    {
        return $this->modules;
    }
}
