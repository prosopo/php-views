<?php

declare(strict_types=1);

namespace Tests\Feature;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View;
use Tests\Helpers\TestTemplateModel;

class ViewsTest extends TestCase
{
    public function testRenderModel(): void
    {
        // given
        vfsStream::setup('templates', null, ['test-template-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new View();
        $model = new TestTemplateModel([
            'message' => 'Hello World!',
        ]);

        // when
        $views->addNamespace('Tests\Helpers', $namespaceConfig);

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }


    // fixme cover other settings as well.
}
