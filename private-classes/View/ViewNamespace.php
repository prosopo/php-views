<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\PrivateClasses\Object\{ObjectClassReader,
    PropertyValueProviderForModels,
    ObjectReader,
    ObjectPropertyWriter,
    PropertyValueProvider,
    PropertyValueProviderByTypes,
    PropertyValueProviderForNullable};
use Prosopo\Views\PrivateClasses\Model\{ModelFactory,
    ModelFactoryWithDefaultsManagement,
    ModelNameResolver,
    ModelNamespaceResolver,
    ModelRenderer,
    ModelRendererWithEventDetails};
use Prosopo\Views\PrivateClasses\EventDispatcher;
use Prosopo\Views\PrivateClasses\Template\FileModelTemplateResolver;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithModelsRender;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewNamespaceModules;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewNamespace
{
    private ViewNamespaceModules $modules;

    /**
     * Using the external ViewFactory and ViewRenderer enables us to seamlessly mix Models from different namespaces,
     * even if they use different template engines, such as Blade and Twig.
     * (see the Views class)
     */
    public function __construct(
        string $namespace,
        ViewNamespaceConfig $config,
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

        $objectPropertyWriter = $modules->getObjectPropertyWriter();
        $objectPropertyWriter = null === $objectPropertyWriter ?
            new ObjectPropertyWriter() :
            $objectPropertyWriter;

        $modelNamespaceProvider = $modules->getModelNamespaceResolver();
        $modelNamespaceProvider = null === $modelNamespaceProvider ?
            new ModelNamespaceResolver(new ObjectClassReader()) :
            $modelNamespaceProvider;

        $modelNameProvider = $modules->getModelNameResolver();
        $modelNameProvider = null === $modelNameProvider ?
            new ModelNameResolver(new ObjectClassReader()) :
            $modelNameProvider;

        $modelTemplateResolver = $modules->getModelTemplateResolver();
        $modelTemplateResolver = null === $modelTemplateResolver ?
            new FileModelTemplateResolver(
                $namespace,
                $config->getTemplatesRootPath(),
                $config->getTemplateFileExtension(),
                $config->fileBasedTemplates(),
                $modelNamespaceProvider,
                $modelNameProvider
            ) :
            $modelTemplateResolver;

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

        $propertyValueProvider = new PropertyValueProviderForNullable($propertyValueProvider);

        // Without null check - templateRenderer is a mandatory module.
        $templateRenderer = $modules->getTemplateRenderer();
        $templateRendererWithModelsRender = new TemplateRendererWithModelsRender(
            $templateRenderer,
            $modelRendererWithNamespace
        );

        if (true === $config->modelsAsStringsInTemplates()) {
            $templateRenderer = $templateRendererWithModelsRender;
        }

        //// 2. Real Factory and Renderer creation (used in the ViewsManager class):

        $realModelFactory = $modules->getModelFactory();
        $realModelFactory = null === $realModelFactory ?
            new ModelFactory(
                $objectReader,
                $propertyValueProvider,
                $modelTemplateResolver,
                $templateRendererWithModelsRender
            ) :
            $realModelFactory;

        $realModelFactory = new ModelFactoryWithDefaultsManagement(
            $realModelFactory,
            // Plain reader, without rendering.
            $objectReader,
            $objectPropertyWriter
        );

        $realModelRenderer = $modules->getModelRenderer();
        $realModelRenderer = null === $realModelRenderer ?
            new ModelRenderer(
                $templateRenderer,
                $modelFactoryWithNamespaces,
                $modelTemplateResolver
            ) :
            $realModelRenderer;

        $realModelRenderer = new ModelRendererWithEventDetails(
            $realModelRenderer,
            $eventDispatcher,
            $templateErrorEventName
        );

        //// 3. Now we can save the objects to the storage.

        $modules->setEventDispatcher($eventDispatcher)
                ->setObjectReader($objectReader)
                ->setObjectPropertyWriter($objectPropertyWriter)
                ->setModelTemplateResolver($modelTemplateResolver)
                ->setPropertyValueProvider($propertyValueProvider)
                ->setModelFactory($realModelFactory)
                ->setModelRenderer($realModelRenderer)
                ->setModelNamespaceResolver($modelNamespaceProvider)
                ->setModelNameResolver($modelNameProvider);

        $this->modules = $modules;
    }

    public function getModules(): ViewNamespaceModules
    {
        return $this->modules;
    }
}
