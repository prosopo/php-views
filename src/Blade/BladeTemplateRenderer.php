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
 * We reserve the right to change its private and protected methods and properties, or introduce new ones.
 */
final class BladeTemplateRenderer implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;

    public function __construct(BladeRendererConfig $config)
    {
        $this->templateRenderer = $this->getOrMakeTemplateRendererWithCustomEscape($config);
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }

    //// Conditional instance retrievals:

    protected function getOrMakeTemplateRendererWithCustomEscape(BladeRendererConfig $config): TemplateRendererInterface
    {
        $templateRendererWithCustomEscape = $config->getTemplateRendererWithCustomEscape();

        if (null !== $templateRendererWithCustomEscape) {
            return $templateRendererWithCustomEscape;
        }

        $templateRenderer = $this->getOrMakeTemplateRenderer($config);

        return $this->makeTemplateRendererWithCustomEscape(
            $templateRenderer,
            $config->getCustomOutputEscapeCallback(),
            $config->getEscapeVariableName()
        );
    }

    protected function getOrMakeTemplateRenderer(BladeRendererConfig $config): TemplateRendererInterface
    {
        $templateRenderer = $config->getTemplateRenderer();

        if (null !== $templateRenderer) {
            return $templateRenderer;
        }

        $templateCompiler = $this->getOrMakeTemplateCompiler($config);
        $templateErrorDispatcher = $this->getOrMakeTemplateErrorDispatcher($config);
        $globalVariables = $config->getGlobalVariables();

        return $this->makeTemplateRenderer($templateCompiler, $templateErrorDispatcher, $globalVariables);
    }

    protected function getOrMakeTemplateCompiler(BladeRendererConfig $config): TemplateCompilerInterface
    {
        $templateCompiler = $config->getTemplateCompiler();

        if (null !== $templateCompiler) {
            return $templateCompiler;
        }

        return $this->makeTemplateCompiler($config->getEscapeVariableName(), $config->getCompilerExtensionCallback());
    }

    protected function getOrMakeTemplateErrorDispatcher(BladeRendererConfig $config): TemplateErrorDispatcherInterface
    {
        $templateErrorDispatcher = $config->getTemplateErrorDispatcher();

        if (null !== $templateErrorDispatcher) {
            return $templateErrorDispatcher;
        }

        return $this->makeTemplateErrorDispatcher($config->getTemplateErrorHandler());
    }

    //// Default instance creators:

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
        ?TemplateErrorDispatcherInterface $templateErrorDispatcher,
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
