<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Template\TemplateRendererWithModelsRender;

class TemplateRendererWithModelsRenderTest extends TestCase
{
    public function testRenderTemplateRendersInnerModels(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelRendererMock = Mockery::mock(ModelRendererInterface::class);
        $renderer = new TemplateRendererWithModelsRender($templateRendererMock, $modelRendererMock);
        $model = Mockery::mock(TemplateModelInterface::class);

        // when
        $result = fn() => $renderer->renderTemplate('<div>{{ content }}</div>', [
            'key1' => 'value1',
            'key2' => $model,
            'key3' => ['nestedKey' => $model],
        ]);

        // then
        $modelRendererMock->shouldReceive('renderModel')
            ->twice()
            ->with($model)
            ->andReturn('<div>Rendered Model</div>');

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ content }}</div>', [
                'key1' => 'value1',
                'key2' => '<div>Rendered Model</div>',
                'key3' => ['nestedKey' => '<div>Rendered Model</div>'],
            ])
            ->andReturn('<div>Final Rendered Output</div>');

        $this->assertEquals('<div>Final Rendered Output</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderTemplateIgnoresNonModelObjects(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelRendererMock = Mockery::mock(ModelRendererInterface::class);
        $renderer = new TemplateRendererWithModelsRender($templateRendererMock, $modelRendererMock);

        $template = '<div>{{ content }}</div>';
        $nonModelObject = new class {
            public string $name = 'NonModelObject';
        };

        $variables = [
            'key1' => 'value1',
            'key2' => 123,
            'key3' => ['nestedKey' => 'string'],
            'key4' => $nonModelObject,
        ];

        // when
        $result = fn() => $renderer->renderTemplate($template, $variables);

        // then
        $modelRendererMock->shouldNotReceive('renderModel');
        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with($template, [
                'key1' => 'value1',
                'key2' => 123,
                'key3' => ['nestedKey' => 'string'],
                'key4' => $nonModelObject,
            ])
            ->andReturn('<div>Final Rendered Output</div>');

        $this->assertEquals('<div>Final Rendered Output</div>', $result());

        // apply
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
