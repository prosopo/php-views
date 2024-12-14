<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRendererWithCustomEscape implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    /**
     * @var callable(mixed $variable): string|null
     */
    private $customOutputEscapeCallback;
    private string $escapeVariableName;

    /**
     * @param callable(mixed $variable): string|null $customOutputEscapeCallback
     */
    public function __construct(
        TemplateRendererInterface $templateRenderer,
        ?callable $customOutputEscapeCallback = null,
        string $escapeVariableName = 'escape'
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->customOutputEscapeCallback = $customOutputEscapeCallback;
        $this->escapeVariableName = $escapeVariableName;
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        $variables = $this->setOutputEscapeCallback(
            $variables,
            $this->escapeVariableName,
            $this->customOutputEscapeCallback
        );

        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }

    /**
     * @param mixed $value
     */
    public function escapeOutput($value): string
    {
        $string_value = $this->caseToString($value);

        return htmlspecialchars($string_value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @param array<string,mixed> $variables
     * @param callable(mixed $variable): string|null $customOutputEscapeCallback
     *
     * @return array<string,mixed>
     */
    protected function setOutputEscapeCallback(
        array $variables,
        string $escapeCallbackName,
        ?callable $customOutputEscapeCallback
    ): array {
        return array_merge(
            $variables,
            [
                $escapeCallbackName => null === $customOutputEscapeCallback ?
                    [$this, 'escapeOutput'] :
                    $customOutputEscapeCallback,
            ]
        );
    }

    /**
     * @param mixed $value
     */
    protected function caseToString($value): string
    {
        if (
            true === is_string($value) ||
            true === is_numeric($value)
        ) {
            return (string)$value;
        }

        if (
            true === is_object($value) &&
            method_exists($value, '__toString')
        ) {
            return (string)$value;
        }

        return '';
    }
}
