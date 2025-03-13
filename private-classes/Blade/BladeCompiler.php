<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Blade;

use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class BladeCompiler implements TemplateCompilerInterface
{
    private string $escapeVariableName;
    /**
     * @var callable(string $template): string|null $extensionCallback
     */
    private $extensionCallback;

    /**
     * @param callable(string $template): string|null $extensionCallback
     */
    public function __construct(string $escapeVariableName, ?callable $extensionCallback)
    {
        $this->escapeVariableName = $escapeVariableName;
        $this->extensionCallback = $extensionCallback;
    }

    public function compileTemplate(string $template): string
    {
        $template = $this->removeComments($template);

        $template = $this->replaceOpeningTagWithBrackets('for', $template);
        $template = $this->replaceOpeningTagWithBrackets('foreach', $template);
        $template = $this->replaceClosingLoops($template);

        $template = $this->replaceOpeningTagWithBrackets('if', $template);
        $template = $this->replaceOpeningTagWithBrackets('elseif', $template);
        $template = $this->replaceClosingIf($template);
        $template = $this->replaceElse($template);

        // With the current regex, it's important to replace echo after 'for' and 'if'
        $template = $this->replaceOpeningEcho($template, $this->escapeVariableName);
        $template = $this->replaceClosingEcho($template);

        $template = $this->replaceOpeningPhp($template);
        $template = $this->replaceClosingPhp($template);

        $template = $this->replaceUseDirective($template);
        $template = $this->replaceSelectedDirective($template);
        $template = $this->replaceCheckedDirective($template);
        $template = $this->replaceClassDirective($template, $this->escapeVariableName);
        $template = $this->replaceSwitchDirectives($template);

        return $this->applyExtensionCallback($template);
    }

    protected function applyExtensionCallback(string $template): string
    {
        if (null !== $this->extensionCallback) {
            $extensionCallback = $this->extensionCallback;

            return $extensionCallback($template);
        }

        return $template;
    }

    // Removes all parts like: "{{--Comment--}}".
    protected function removeComments(string $template): string
    {
        return (string) preg_replace('/{{--[\s\S]*?--}}/', '', $template);
    }

    protected function replaceOpeningEcho(string $template, string $escape_callback_name): string
    {
        $template = str_replace('{{', sprintf('<?php echo $%s(', $escape_callback_name), $template);

        return str_replace('{!!', '<?php echo(', $template);
    }

    protected function replaceClosingEcho(string $template): string
    {
        $template = str_replace('}}', '); ?>', $template);

        return str_replace('!!}', '); ?>', $template);
    }

    protected function getRegexForTagWithBrackets(string $tag): string
    {
        // Without 's' flag, .* means everything, but not the new line.
        // It's necessary to separate the new line,
        // otherwise we'll have troubles with the nested things, like "for(test()->new()) {{ new() }} @endforeach".
        return sprintf('/@%s\s*\((.*)\)/', preg_quote($tag, '/'));
    }

    protected function replaceOpeningTagWithBrackets(string $tag, string $template): string
    {
        $regex = $this->getRegexForTagWithBrackets($tag);
        $replacement = sprintf('<?php %s( $1 ): ?>', $tag);

        return (string) preg_replace($regex, $replacement, $template);
    }

    protected function replaceClosingLoops(string $template): string
    {
        // It's important to put 'endforeach' first, because 'endfor' is a part of 'endforeach'.
        $template = str_replace('@endforeach', '<?php endforeach; ?>', $template);

        return str_replace('@endfor', '<?php endfor; ?>', $template);
    }

    protected function replaceClosingIf(string $template): string
    {
        return str_replace('@endif', '<?php endif; ?>', $template);
    }

    protected function replaceElse(string $template): string
    {
        return str_replace('@else', '<?php else: ?>', $template);
    }

    protected function replaceOpeningPhp(string $template): string
    {
        return str_replace('@php', '<?php', $template);
    }

    protected function replaceClosingPhp(string $template): string
    {
        return str_replace('@endphp', '?>', $template);
    }

    protected function replaceUseDirective(string $template): string
    {
        return (string) preg_replace('/@use\s*\((["\'])(.*?)\1\)/s', '<?php use $2; ?>', $template);
    }

    protected function replaceSelectedDirective(string $template): string
    {
        $regex = $this->getRegexForTagWithBrackets('selected');

        $replacement = '<?php if ( $1 ) echo "selected=\"\""; ?>';

        return (string) preg_replace($regex, $replacement, $template);
    }

    protected function replaceCheckedDirective(string $template): string
    {
        $regex = $this->getRegexForTagWithBrackets('checked');

        $replacement = '<?php if ( $1 ) echo "checked=\"\""; ?>';

        return (string) preg_replace($regex, $replacement, $template);
    }

    protected function replaceSwitchDirectives(string $template): string
    {
        $template = $this->replaceOpeningTagWithBrackets('case', $template);

        // 1. remove space between @switch and the first "case",
        // otherwise it'll case an error (spaces are threat as unexpected HTML).
        $regex = '/@switch\s*\((.*)\)\s*<\?php/';
        $template = (string) preg_replace($regex, '<?php switch($1): ?><?php', $template);

        $template = str_replace('@break', '<?php break; ?>', $template);
        $template = str_replace('@default', '<?php default: ?>', $template);
        $template = str_replace('@endswitch', '<?php endswitch; ?>', $template);

        return $template;
    }

    // "@class(['name', 'name2' => $condition])" to 'class="name name2"'.
    protected function replaceClassDirective(string $template, string $escape_callback_name): string
    {
        $regex = '/@class\s*\((\[.*])\)/s';
        $replacement = $this->getCodeForConditionClasses($escape_callback_name);

        return (string) preg_replace($regex, $replacement, $template);
    }

    protected function getCodeForConditionClasses(string $escape_callback_name): string
    {
        $php_code = array();

        $php_code[] = '<?php echo "class=\"";';
        $php_code[] = 'ob_start();';
        $php_code[] = 'foreach ( $1 as \$key => \$value ) {';
        $php_code[] = sprintf('if ( true === is_int( \$key ) ) { echo $%s(\$value) . " "; }', $escape_callback_name);
        $php_code[] = sprintf('else { if ( true === \$value ) { echo $%s(\$key) . " "; } }', $escape_callback_name);
        $php_code[] = '}';// foreach.
        $php_code[] = 'echo trim( (string)ob_get_clean() );';
        $php_code[] = 'echo "\""; ?>';

        return implode("\n", $php_code);
    }
}
