<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\PrivateClasses\Template\TemplateRenderer;

class TemplateRendererTest extends TestCase
{
    public function testRenderTemplateReturnsRenderedContent(): void
    {
        // given
        $templateExecutor = Mockery::mock(CodeExecutorInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        $result = fn() => $renderer->renderTemplate('<div>{{ $var }}</div>', ['var' => 'Test Content']);

        // then
        $templateExecutor->shouldReceive('executeCode')
            ->once()
            ->with('<div>{{ $var }}</div>', ['var' => 'Test Content'])
            ->andReturnUsing(function () {
                echo '<div>Test Content</div>';
            });
        $this->assertSame('<div>Test Content</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateDoesNotPrintByDefault(): void
    {
        // given
        $templateExecutor = Mockery::mock(CodeExecutorInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        ob_start();
        $result = fn() => $renderer->renderTemplate('<p>{{ $message }}</p>', ['message' => 'Hello, World!']);
        $output = ob_get_clean();

        // then
        $templateExecutor->shouldReceive('executeCode')
            ->once()
            ->with('<p>{{ $message }}</p>', ['message' => 'Hello, World!'])
            ->andReturnUsing(function () {
                echo '<p>Hello, World!</p>';
            });

        $this->assertSame('<p>Hello, World!</p>', $result());
        $this->assertSame('', $output);

        // apply
        Mockery::close();
    }

    public function testRenderTemplatePrintsWhenDoPrintIsTrue(): void
    {
        // given
        $templateExecutor = Mockery::mock(CodeExecutorInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        $result = fn() => $renderer->renderTemplate('<h1>{{ $title }}</h1>', ['title' => 'Welcome'], true);

        // then
        $templateExecutor->shouldReceive('executeCode')
            ->once()
            ->with('<h1>{{ $title }}</h1>', ['title' => 'Welcome'])
            ->andReturnUsing(function () {
                echo '<h1>Welcome</h1>';
            });

        ob_start();
        $rendered = $result();
        $output = ob_get_clean();

        $this->assertSame('<h1>Welcome</h1>', $rendered);
        $this->assertSame('<h1>Welcome</h1>', $output);

        // apply
        Mockery::close();
    }

    public function testRenderTemplateHandlesEmptyTemplate(): void
    {
        // given
        $templateExecutor = Mockery::mock(CodeExecutorInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        $result = fn() => $renderer->renderTemplate('', []);

        // then
        $templateExecutor->shouldReceive('executeCode')
            ->once()
            ->with('', [])
            ->andReturnUsing(function () {
                echo '';
            });

        $this->assertSame('', $result());

        // apply
        Mockery::close();
    }
}
