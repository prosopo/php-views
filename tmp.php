<?php

use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\View\ViewTemplateRendererConfig;
use Prosopo\Views\ViewsManager;

class CompilerStubForPlainPhpSupport implements TemplateCompilerInterface
{
    public function compileTemplate(string $template): string
    {
        return $template;
    }
}

// ...

$viewTemplateRendererConfig = new ViewTemplateRendererConfig();
$viewTemplateRendererConfig->getModules()
    ->setTemplateCompiler(new CompilerStubForPlainPhpSupport());

$viewTemplateRenderer = new ViewTemplateRenderer($viewTemplateRendererConfig);

$views = new ViewsManager();

$viewNamespaceConfig = new ViewNamespaceConfig($viewTemplateRenderer);
$viewNamespaceConfig
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setTemplateFileExtension('.php');

$views->registerNamespace('MyApp\Models', $viewNamespaceConfig);
