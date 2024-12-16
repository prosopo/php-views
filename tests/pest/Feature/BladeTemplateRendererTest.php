<?php

declare(strict_types=1);

namespace Tests\Feature;

use org\bovigo\vfs\vfsStream;
use ParseError;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;

class BladeTemplateRendererTest extends TestCase
{
    public function testRendersFile(): void
    {
        // given
        vfsStream::setup('templates', null, ['template.blade.php' => '@if($var)The variable is set.@endif']);
        $bladeRendererConfig = new BladeRendererConfig();
        $bladeRenderer = new BladeTemplateRenderer($bladeRendererConfig);

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
        $bladeRendererConfig = new BladeRendererConfig();
        $bladeRendererConfig->setIsFileBasedTemplate(false);
        $bladeRenderer = new BladeTemplateRenderer($bladeRendererConfig);

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
        $bladeRendererConfig = new BladeRendererConfig();
        $bladeRendererConfig->setIsFileBasedTemplate(false);
        $receivedEventDetails = null;
        $bladeRendererConfig->setTemplateErrorHandler(function (array $eventDetails) use (&$receivedEventDetails) {
            $receivedEventDetails = $eventDetails;
        });
        $bladeRenderer = new BladeTemplateRenderer($bladeRendererConfig);

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

    public function testTemplateErrorHandlerNotCalledWithoutReason(): void{
        // fixme
    }

    // fixme cover other settings as well.
}
