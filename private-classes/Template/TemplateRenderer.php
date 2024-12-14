<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Error;
use Exception;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRenderer implements TemplateRendererInterface
{
    private TemplateCompilerInterface $templateCompiler;
    private ?TemplateErrorDispatcherInterface $errorDispatcher;
    /**
     * @var array<string,mixed>
     */
    private array $globalVariables;

    /**
     * @param array<string,mixed> $globalVariables
     */
    public function __construct(
        TemplateCompilerInterface $template_engine,
        ?TemplateErrorDispatcherInterface $errorDispatcher = null,
        array $globalVariables = []
    ) {
        $this->templateCompiler = $template_engine;
        $this->errorDispatcher = $errorDispatcher;
        $this->globalVariables = $globalVariables;
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        $variables = array_merge($this->globalVariables, $variables);

        $phpTemplate = $this->templateCompiler->compile($template);

        // @phpcs:ignore
        extract($variables);

        ob_start();

        try {
            // Catch all level-errors and turn into the generic error.
            // @phpcs:ignore
            set_error_handler(
                function ($errno, $errstr) {
                    // @phpcs:ignore
                    throw new Error($errstr, $errno);
                }
            );

            // @phpcs:ignore
            eval('?>' . $phpTemplate);
        } catch (Error $error) {
            $this->dispatchError($error, $template, $phpTemplate, $variables);
        } catch (Exception $error) {
            // Separate catch handlers to handle all the error types, cause some errors do not inherit Error.
            $this->dispatchError($error, $template, $phpTemplate, $variables);
        } finally {
            restore_error_handler();
        }

        $html = (string)ob_get_clean();

        if (true === $doPrint) {
            // @phpcs:ignore
            echo $html;
        }

        return $html;
    }

    /**
     * @param Error|Exception $errorOrException
     * @param array<string,mixed> $variables
     */
    protected function dispatchError(
        $errorOrException,
        string $template,
        string $compiledPhpTemplate,
        array $variables
    ): void {
        if (null === $this->errorDispatcher) {
            return;
        }

        $this->errorDispatcher->dispatch($errorOrException, $template, $compiledPhpTemplate, $variables);
    }
}
