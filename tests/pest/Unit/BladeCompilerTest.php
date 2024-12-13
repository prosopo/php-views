<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Prosopo\Views\Blade\BladeCompiler;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Tests\Helpers\TemplatesHelper;
use Tests\TestCase;

class BladeCompilerTest extends TestCase
{
    public static function getTemplatesHelper(): TemplatesHelper
    {
        return new TemplatesHelper();
    }

    /**
     * @return array<string,string>
     */
    public static function templateNamesProvider(): array
    {
        $templatesHelper = self::getTemplatesHelper();

        $templates       = $templatesHelper->getTemplatesByExtension('.blade.php', [
            __DIR__ . '/../../templates',
            __DIR__ . '/../../templates/inline',
            __DIR__ . '/../../templates/multiline',
        ]);

        $testArguments   = array_map(function (string $template) {
            return [$template];
        }, $templates);

        $testNames = array_map(
            function (string $template) {
                $shortDirName = basename(dirname($template));

                $fileName     = pathinfo($template, PATHINFO_FILENAME);

                return $shortDirName . '/' . $fileName;
            },
            $templates
        );

        return array_combine($testNames, $testArguments);
    }

    #[DataProvider('templateNamesProvider')]
    public function testTemplateCompilation(string $template): void
    {
        $compiler = $this->getCompiler();

        $templatesHelper = self::getTemplatesHelper();
        $phpTemplate     = $templatesHelper->getTemplate($template . '.php');
        $bladeTemplate   = $templatesHelper->getTemplate($template . '.blade.php');

        $compiledPhp = $compiler->compile($bladeTemplate);

        $this->assertEquals(
            $phpTemplate,
            $compiledPhp,
            'Failed to compile template: ' . pathinfo($template, PATHINFO_FILENAME)
        );
    }

    protected function getCompiler(): TemplateCompilerInterface
    {
        return new BladeCompiler('e');
    }
}
