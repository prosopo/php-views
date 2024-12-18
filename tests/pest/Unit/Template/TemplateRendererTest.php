<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\PrivateClasses\Template\TemplateRenderer;

class TemplateRendererTest extends TestCase
{
    public function testRenderTemplateReturnsRenderedContent(): void
    {
        // given
        $templateExecutor = Mockery::mock(CodeRunnerInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        $result = fn() => $renderer->renderTemplate('<div>{{ $var }}</div>', ['var' => 'Test Content']);

        // then
        $templateExecutor->shouldReceive('runCode')
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
        $templateExecutor = Mockery::mock(CodeRunnerInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        ob_start();
        $result = fn() => $renderer->renderTemplate('<p>{{ $message }}</p>', ['message' => 'Hello, World!']);
        $output = ob_get_clean();

        // then
        $templateExecutor->shouldReceive('runCode')
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

    public function testRenderTemplateHandlesEmptyTemplate(): void
    {
        // given
        $templateExecutor = Mockery::mock(CodeRunnerInterface::class);
        $renderer = new TemplateRenderer($templateExecutor);

        // when
        $result = fn() => $renderer->renderTemplate('', []);

        // then
        $templateExecutor->shouldReceive('runCode')
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
