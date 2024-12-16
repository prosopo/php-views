<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Config\BladeRendererConfigInterface;
use Prosopo\Views\Interfaces\Modules\RendererModulesContainerInterface;
use Prosopo\Views\Interfaces\Modules\RendererModulesInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Blade\BladeCompiler;
use Prosopo\Views\PrivateClasses\CodeExecutor\CodeExecutorWithErrorEvent;
use Prosopo\Views\PrivateClasses\CodeExecutor\CodeExecutorWithGlobalArguments;
use Prosopo\Views\PrivateClasses\CodeExecutor\CodeExecutorWithTemplateCompilation;
use Prosopo\Views\PrivateClasses\CodeExecutor\PhpCodeExecutor;
use Prosopo\Views\PrivateClasses\EventDispatcher;
use Prosopo\Views\PrivateClasses\Template\TemplateRenderer;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithCustomEscape;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithEventDetails;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithFileTemplate;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties, and introduce new public ones.
 */
final class BladeTemplateRenderer implements TemplateRendererInterface, RendererModulesContainerInterface
{
    private TemplateRendererInterface $templateRenderer;
    private RendererModulesInterface $modules;

    public function __construct(BladeRendererConfigInterface $config)
    {
        $modules = clone $config->getModules();

        $errorEventName = $config->getTemplateErrorEventName();

        $eventDispatcher = $modules->getEventDispatcher();
        $eventDispatcher = null === $eventDispatcher ?
            new EventDispatcher() :
            $eventDispatcher;

        $templateErrorHandler = $config->getTemplateErrorHandler();
        if (null !== $templateErrorHandler) {
            $eventDispatcher->addEventListener($errorEventName, $templateErrorHandler);
        }

        $templateCompiler = $modules->getTemplateCompiler();
        $templateCompiler = null === $templateCompiler ?
            new BladeCompiler($config->getEscapeVariableName(), $config->getCompilerExtensionCallback()) :
            $templateCompiler;

        $codeExecutor = $modules->getCodeExecutor();
        $codeExecutor = null === $codeExecutor ?
            new PhpCodeExecutor() :
            $codeExecutor;

        $codeExecutor = new CodeExecutorWithErrorEvent($codeExecutor, $eventDispatcher, $errorEventName);
        $codeExecutor = new CodeExecutorWithGlobalArguments($codeExecutor, $config->getGlobalVariables());
        $codeExecutor = new CodeExecutorWithTemplateCompilation($codeExecutor, $templateCompiler);

        $templateRenderer = $modules->getTemplateRenderer();
        $templateRenderer = null === $templateRenderer ?
            new TemplateRenderer($codeExecutor) :
            $templateRenderer;

        if (true === $config->isFileBasedTemplate()) {
            $templateRenderer = new TemplateRendererWithFileTemplate($templateRenderer);
        }

        $templateRenderer = new TemplateRendererWithCustomEscape(
            $templateRenderer,
            $config->getCustomOutputEscapeCallback(),
            $config->getEscapeVariableName()
        );
        $templateRenderer = new TemplateRendererWithEventDetails($templateRenderer, $eventDispatcher, $errorEventName);

        $modules->setEventDispatcher($eventDispatcher)
                ->setTemplateCompiler($templateCompiler)
                ->setTemplateRenderer($templateRenderer)
                ->setCodeExecutor($codeExecutor);

        $this->modules = $modules;
        $this->templateRenderer = $templateRenderer;
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }

    public function getModules(): RendererModulesInterface
    {
        return $this->modules;
    }
}
