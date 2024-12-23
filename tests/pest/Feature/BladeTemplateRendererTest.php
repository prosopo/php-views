<?php

declare(strict_types=1);

namespace Tests\Feature;

use org\bovigo\vfs\vfsStream;
use ParseError;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\View\ViewTemplateRendererConfig;
use Prosopo\Views\View\ViewTemplateRenderer;

class BladeTemplateRendererTest extends TestCase
{
    public function testRendersFile(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '@if($var)The variable is set.@endif']);
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when
        $result = $bladeRenderer->renderTemplate(vfsStream::url('templates/template.blade.php'), [
            'var' => true
        ]);

        // then
        $this->assertSame('The variable is set.', $result);
    }

    public function testRendersStringWhenFileBasedFlagIsDisabled(): void
    {
        // given
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setFileBasedTemplates(false);
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when
        $result = $bladeRenderer->renderTemplate('@if($var)The variable is set.@endif', [
            'var' => true
        ]);

        // then
        $this->assertSame('The variable is set.', $result);
    }

    public function testTemplateErrorHandlerIsCalledOnError(): void
    {
        // given
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setFileBasedTemplates(false);
        $receivedEventDetails = null;
        $bladeRendererConfig->setTemplateErrorHandler(function (array $eventDetails) use (&$receivedEventDetails) {
            $receivedEventDetails = $eventDetails;
        });
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when
        $bladeRenderer->renderTemplate('@if($var)wrong template', [
            'var' => true
        ]);

        // then
        $this->assertSame(['template','arguments','code','error',], array_keys($receivedEventDetails));

        $this->assertSame('@if($var)wrong template', $receivedEventDetails['template']);
        $this->assertTrue($receivedEventDetails['arguments']['var']);
        $this->assertSame('<?php if( $var ): ?>wrong template', $receivedEventDetails['code']);
        $this->assertSame(ParseError::class, get_class($receivedEventDetails['error']));
    }

    public function testTemplateErrorHandlerNotCalledWithoutReason(): void
    {
        // given
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setFileBasedTemplates(false);
        $receivedEventDetails = null;
        $bladeRendererConfig->setTemplateErrorHandler(function (array $eventDetails) use (&$receivedEventDetails) {
            $receivedEventDetails = $eventDetails;
        });
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when
        $bladeRenderer->renderTemplate('@if($var)good template@endif', [
            'var' => true
        ]);

        // then
        $this->assertNull($receivedEventDetails);
    }

    public function testEscapesOutput(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '{{ $var }}']);
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when
        $result = $bladeRenderer->renderTemplate(vfsStream::url('templates/template.blade.php'), [
            'var' => '<script>alert("XSS");</script>'
        ]);

        // then
        $this->assertSame('&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;', $result);
    }

    public function testCallsCustomEscapeCallback(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '{{ $var }}']);
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setCustomOutputEscapeCallback(fn($value) => 'custom-escaped-' . $value);
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when

        $result = $bladeRenderer->renderTemplate(vfsStream::url('templates/template.blade.php'), [
            'var' => 'data'
        ]);

        // then
        $this->assertSame('custom-escaped-data', $result);
    }

    public function testAddsGlobalVariable(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '{{ $global }}']);
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setGlobalVariables(['global' => 'Top-Level']);
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when

        $result = $bladeRenderer->renderTemplate(vfsStream::url('templates/template.blade.php'));

        // then
        $this->assertSame('Top-Level', $result);
    }

    public function testAddsGlobalVariableMethod(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '{{ $calc(1,2) }}']);
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setGlobalVariables(['calc' => function ($a, $b) {
            return $a + $b;
        }]);
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when

        $result = $bladeRenderer->renderTemplate(vfsStream::url('templates/template.blade.php'));

        // then
        $this->assertSame('3', $result);
    }

    public function testCallsCompilerExtensionCallback(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '@custom template']);
        $bladeRendererConfig = new ViewTemplateRendererConfig();
        $bladeRendererConfig->setCompilerExtensionCallback(function (string $template) {
            return str_replace('@custom', '_custom_', $template);
        });
        $bladeRenderer = new ViewTemplateRenderer($bladeRendererConfig);

        // when

        $result = $bladeRenderer->renderTemplate(vfsStream::url('templates/template.blade.php'));

        // then
        $this->assertSame('_custom_ template', $result);
    }
}
