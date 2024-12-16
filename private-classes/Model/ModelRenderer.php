<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Closure;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelRenderer implements ModelRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private ModelFactoryInterface $viewFactory;
    private ModelTemplateProviderInterface $templateProvider;

    public function __construct(
        TemplateRendererInterface $templateRenderer,
        ModelFactoryInterface $modelFactory,
        ModelTemplateProviderInterface $templateProvider
    ) {
        $this->templateRenderer       = $templateRenderer;
        $this->viewFactory            = $modelFactory;
        $this->templateProvider = $templateProvider;
    }

    public function renderModel($modelOrClass, ?Closure $setupCallback = null, bool $doPrint = false): string
    {
        $model = true === is_string($modelOrClass) ?
            $this->viewFactory->makeModel($modelOrClass) :
            $modelOrClass;

        if (null !== $setupCallback) {
            $setupCallback($model);
        }

        $variables = $model->getTemplateArguments();
        $template  = $this->templateProvider->getModelTemplate($model);

        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }
}
