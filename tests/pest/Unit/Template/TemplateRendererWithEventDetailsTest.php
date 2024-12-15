<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithEventDetails;

class TemplateRendererWithEventDetailsTest extends TestCase
{
    public function testRenderTemplateAttachesAndDetachesEventDetails(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $renderer = new TemplateRendererWithEventDetails($templateRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $result = fn() => $renderer->renderTemplate('<div>{{ $var }}</div>', ['var' => 'Test Content']);

        // then
        $eventDispatcherMock->shouldReceive('attachEventDetails')
            ->once()
            ->with('render_event', ['template' => '<div>{{ $var }}</div>',]);

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $var }}</div>', ['var' => 'Test Content'], false)
            ->andReturn('<div>Test Content</div>');

        $eventDispatcherMock->shouldReceive('detachEventDetails')
            ->once()
            ->with('render_event', ['template' => '<div>{{ $var }}</div>']);

        $this->assertSame('<div>Test Content</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplatePassesDoPrintFlag(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $renderer = new TemplateRendererWithEventDetails($templateRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $result = fn() => $renderer->renderTemplate(
            '<p>{{ $message }}</p>',
            ['message' => 'Hello, World!'],
            true
        );

        // then
        $eventDispatcherMock->shouldReceive('attachEventDetails')
            ->once()
            ->with('render_event', ['template' => '<p>{{ $message }}</p>']);

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<p>{{ $message }}</p>', ['message' => 'Hello, World!'], true)
            ->andReturn('<p>Hello, World!</p>');

        $eventDispatcherMock->shouldReceive('detachEventDetails')
            ->once()
            ->with('render_event', ['template' => '<p>{{ $message }}</p>']);

        $this->assertSame('<p>Hello, World!</p>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateHandlesEmptyTemplate(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $renderer = new TemplateRendererWithEventDetails($templateRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $result = fn() => $renderer->renderTemplate('', []);

        // then
        $eventDispatcherMock->shouldReceive('attachEventDetails')
            ->once()
            ->with('render_event', ['template' => '']);

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('', [], false)
            ->andReturn('');

        $eventDispatcherMock->shouldReceive('detachEventDetails')
            ->once()
            ->with('render_event', ['template' => '']);

        $this->assertSame('', $result());

        // apply
        Mockery::close();
    }
}
