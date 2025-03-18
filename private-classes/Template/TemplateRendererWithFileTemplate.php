<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\Template\FileTemplateContentProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRendererWithFileTemplate implements TemplateRendererInterface
{
    private FileTemplateContentProviderInterface $fileTemplateContentProvider;
    private TemplateRendererInterface $templateRenderer;

    public function __construct(
        FileTemplateContentProviderInterface $fileTemplateContentProvider,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->fileTemplateContentProvider = $fileTemplateContentProvider;
        $this->templateRenderer = $templateRenderer;
    }

    public function renderTemplate(string $template, array $variables = []): string
    {
        $template = $this->fileTemplateContentProvider->getFileTemplateContent($template);

        return $this->templateRenderer->renderTemplate($template, $variables);
    }
}
