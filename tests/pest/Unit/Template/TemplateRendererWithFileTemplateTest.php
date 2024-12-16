<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithFileTemplate;

class TemplateRendererWithFileTemplateTest extends TestCase
{
    public function testRenderTemplateReadsFileAndRendersContent(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'template.blade.php' =>  '<div>{{ $key }}</div>',
        ]);
        $templateFilePath = vfsStream::url('templates/template.blade.php');
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $renderer = new TemplateRendererWithFileTemplate($templateRendererMock);

        // when
        $result = fn()=> $renderer->renderTemplate($templateFilePath, ['key' => 'value']);

        // then
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $key }}</div>', ['key' => 'value'], false)
            ->andReturn('<div>value</div>');

        $this->assertSame('<div>value</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateReturnsEmptyStringForMissingFile(): void
    {
        // given
        vfsStream::setup('templates');
        $missingTemplateFilePath = vfsStream::url('templates/missing-template.blade.php');
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $renderer = new TemplateRendererWithFileTemplate($templateRendererMock);

        // when
        $result = fn() => $renderer->renderTemplate($missingTemplateFilePath, ['key' => 'value']);

        // then
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('', ['key' => 'value'], false)
            ->andReturn('');

        $this->assertSame('', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateHandlesDoPrintFlag(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'template.blade.php' => '<div>{{ $key }}</div>',
        ]);
        $templateFilePath = vfsStream::url('templates/template.blade.php');
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $renderer = new TemplateRendererWithFileTemplate($templateRendererMock);

        // when
        $result = fn() => $renderer->renderTemplate($templateFilePath, ['key' => 'value'], true);

        // then
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $key }}</div>', ['key' => 'value'], true)
            ->andReturn('<div>value</div>');

        $this->assertSame('<div>value</div>', $result());

        // apply
        Mockery::close();
    }
}
