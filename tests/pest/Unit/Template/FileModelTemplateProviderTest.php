<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Template\FileModelTemplateProvider;

class FileModelTemplateProviderTest extends TestCase
{
    public function testGetTemplateReturnsFileContentForValidView(): void
    {
        // given
         vfsStream::setup('templates', null, ['sample-view.blade.php' => 'View Content']);
         $templateModel = Mockery::mock(TemplateModelInterface::class);
         $provider = new FileModelTemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        // when
        $result = fn() => $provider->getModelTemplate($templateModel);

        // then
        $this->assertSame('View Content', $result());

        Mockery::close();
    }

   /* public function testGetTemplateReturnsEmptyStringForMissingFile(): void
    {
        // given
        vfsStream::setup('templates'); // Empty directory
        $provider = new FileModelTemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\MissingView';

        // when
        $result = fn() => $provider->getModelTemplate($viewClass);

        // then
        $this->assertSame('', $result());
    }

    public function testGetTemplateHandlesCamelCaseConversion(): void
    {
        // given
        vfsStream::setup('templates', null, ['some-camel-case-view.blade.php' => 'Camel Case Content']);
        $provider = new FileModelTemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\SomeCamelCaseView';

        // when
        $result = fn() => $provider->getModelTemplate($viewClass);

        // then
        $this->assertSame('Camel Case Content', $result());
    }

    public function testGetTemplateHandlesNestedNamespaces(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'admin/dashboard-view.blade.php' => 'Dashboard Content'
        ]);
        $provider = new FileModelTemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\Admin\\DashboardView';

        // when
        $result = fn() => $provider->getModelTemplate($viewClass);

        // then
        $this->assertSame('Dashboard Content', $result());
    }*/
}
