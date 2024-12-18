<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithCustomEscape;

class TemplateRendererWithCustomEscapeTest extends TestCase
{
    public function testRenderTemplateUsesCustomEscapeCallback(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $customEscapeCallback = fn($value) => strtoupper((string)$value);
        $renderer = new TemplateRendererWithCustomEscape(
            $templateRendererMock,
            $customEscapeCallback,
            'escape'
        );

        // when
        $result = fn() => $renderer->renderTemplate('<div>{{ $escape($var) }}</div>', ['var' => 'test']);

        // then
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with(
                '<div>{{ $escape($var) }}</div>',
                Mockery::on(function ($variables) {
                    return is_callable($variables['escape'])
                        && call_user_func($variables['escape'], 'test') === 'TEST';
                })
            )
            ->andReturn('<div>TEST</div>');

        $this->assertSame('<div>TEST</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateUsesDefaultEscapeCallback(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $renderer = new TemplateRendererWithCustomEscape(
            $templateRendererMock,
            null,
            'escape'
        );

        // when
        $result = fn() => $renderer->renderTemplate(
            '<div>{{ $escape($var) }}</div>',
            ['var' => '<script>alert("test")</script>']
        );

        // then
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with(
                '<div>{{ $escape($var) }}</div>',
                Mockery::on(function ($variables) {
                    return is_callable($variables['escape'])
                        && call_user_func($variables['escape'], '<script>alert("test")</script>') === '&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;';
                })
            )
            ->andReturn('<div>&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;</div>');

        $this->assertSame('<div>&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateMergesVariablesWithEscapeCallback(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $renderer = new TemplateRendererWithCustomEscape(
            $templateRendererMock,
            null,
            'escape'
        );

        // when
        $result = fn() => $renderer->renderTemplate('<div>{{ $escape($var) }}</div>', ['var' => 'hello']);

        // then
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with(
                '<div>{{ $escape($var) }}</div>',
                Mockery::on(function ($mergedVariables) {
                    return isset($mergedVariables['escape']) &&
                        is_callable($mergedVariables['escape']) &&
                        $mergedVariables['escape']('hello') === 'hello';
                })
            )
            ->andReturn('<div>hello</div>');

        $this->assertSame('<div>hello</div>', $result());

        // apply
        Mockery::close();
    }
}
