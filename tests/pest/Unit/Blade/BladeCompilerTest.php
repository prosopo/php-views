<?php

declare(strict_types=1);

namespace Tests\Unit\Blade;

use PHPUnit\Framework\Attributes\DataProvider;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\PrivateClasses\Blade\BladeCompiler;
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
            __DIR__ . '/../../../templates',
            __DIR__ . '/../../../templates/inline',
            __DIR__ . '/../../../templates/multiline',
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

        $compiledPhp = $compiler->compileTemplate($bladeTemplate);

        $this->assertNotEmpty($phpTemplate); // to make sure the path is right.
        $this->assertEquals(
            $phpTemplate,
            $compiledPhp,
            'Failed to compile template: ' . pathinfo($template, PATHINFO_FILENAME)
        );
    }

    public function testExtensionCallbackIsCalled(): void
    {
        // given
        $callbackCalled = false;
        $extensionCallback = function (string $template) use (&$callbackCalled): string {
            $callbackCalled = true;
            return $template . ' extended';
        };
        $compiler = new BladeCompiler('escape', $extensionCallback);

        // when
        $result = $compiler->compileTemplate('original template');

        // then
        $this->assertTrue($callbackCalled);
        $this->assertSame('original template extended', $result);
    }

    protected function getCompiler(): TemplateCompilerInterface
    {
        return new BladeCompiler('e', null);
    }
}
