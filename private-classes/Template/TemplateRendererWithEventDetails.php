<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRendererWithEventDetails implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private EventDispatcherInterface $eventDispatcher;
    private string $eventName;

    public function __construct(
        TemplateRendererInterface $templateRenderer,
        EventDispatcherInterface $eventDispatcher,
        string $eventName
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->eventDispatcher = $eventDispatcher;
        $this->eventName = $eventName;
    }

    public function renderTemplate(string $template, array $variables = []): string
    {
        $eventDetails = [
            'template' => $template,
        ];

        $this->eventDispatcher->registerEventDetails($this->eventName, $eventDetails);

        $response = $this->templateRenderer->renderTemplate($template, $variables);

        $this->eventDispatcher->unregisterEventDetails($this->eventName, $eventDetails);

        return $response;
    }
}
