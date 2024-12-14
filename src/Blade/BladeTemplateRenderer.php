<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Blade\BladeCompiler;
use Prosopo\Views\PrivateClasses\Template\TemplateErrorDispatcher;
use Prosopo\Views\PrivateClasses\Template\TemplateRenderer;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithCustomEscape;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, or introduce new public ones.
 */
final class BladeTemplateRenderer implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private BladeRendererModules $modules;

    public function __construct(BladeRendererConfig $config)
    {
        $modules = clone $config->getModules();

        $templateErrorDispatcher = $modules->getTemplateErrorDispatcher();
        $templateErrorDispatcher = null === $templateErrorDispatcher ?
            $this->makeTemplateErrorDispatcher($config->getTemplateErrorHandler()) :
            $templateErrorDispatcher;

        $templateCompiler = $modules->getTemplateCompiler();
        $templateCompiler = null === $templateCompiler ?
            $this->makeTemplateCompiler($config->getEscapeVariableName(), $config->getCompilerExtensionCallback()) :
            $templateCompiler;

        $templateRenderer = $modules->getTemplateRenderer();
        $templateRenderer = null === $templateRenderer ?
            $this->makeTemplateRenderer($templateCompiler, $templateErrorDispatcher, $config->getGlobalVariables()) :
            $templateRenderer;

        $templateRendererWithCustomEscape = $modules->getTemplateRendererWithCustomEscape();
        $templateRendererWithCustomEscape = null === $templateRendererWithCustomEscape ?
            $this->makeTemplateRendererWithCustomEscape(
                $templateRenderer,
                $config->getCustomOutputEscapeCallback(),
                $config->getEscapeVariableName()
            ) :
            $templateRendererWithCustomEscape;

        $modules->setTemplateErrorDispatcher($templateErrorDispatcher)
                ->setTemplateCompiler($templateCompiler)
                ->setTemplateRenderer($templateRenderer)
                ->setTemplateRendererWithCustomEscape($templateRendererWithCustomEscape);

        $this->templateRenderer = $templateRendererWithCustomEscape;
        $this->modules = $modules;
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }

    public function getModules(): BladeRendererModules
    {
        return $this->modules;
    }

    /**
     * @param callable(TemplateErrorInterface $templateError): void|null $errorHandler
     */
    protected function makeTemplateErrorDispatcher(?callable $errorHandler): TemplateErrorDispatcherInterface
    {
        return new TemplateErrorDispatcher($errorHandler);
    }

    /**
     * @param callable(string $template): string|null $compilerExtensionCallback
     */
    protected function makeTemplateCompiler(
        string $escapeVariableName,
        ?callable $compilerExtensionCallback
    ): TemplateCompilerInterface {
        return new BladeCompiler($escapeVariableName, $compilerExtensionCallback);
    }

    /**
     * @param array<string,mixed> $globalVariables
     */
    protected function makeTemplateRenderer(
        TemplateCompilerInterface $templateCompiler,
        TemplateErrorDispatcherInterface $templateErrorDispatcher,
        array $globalVariables
    ): TemplateRendererInterface {
        return new TemplateRenderer(
            $templateCompiler,
            $templateErrorDispatcher,
            $globalVariables
        );
    }

    /**
     * @param callable(mixed $variable): string|null $customOutputEscapeCallback $customOutputEscapeCallback
     */
    protected function makeTemplateRendererWithCustomEscape(
        TemplateRendererInterface $templateRenderer,
        ?callable $customOutputEscapeCallback,
        string $escapeVariableName
    ): TemplateRendererInterface {
        return new TemplateRendererWithCustomEscape(
            $templateRenderer,
            $customOutputEscapeCallback,
            $escapeVariableName
        );
    }
}
