<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\View\ViewTemplateRendererConfig;
use Prosopo\Views\View\ViewTemplateRendererModules;

class ViewTemplateRendererTest extends TestCase
{
    //// testMakes

    public function testMakesTemplateRenderer(): void
    {
        $this->testUsesGivenModule(null, function (ViewTemplateRendererModules $modules) {
            $this->assertNotNull($modules->getTemplateRenderer());
        });
    }

    public function testMakesEventDispatcher(): void
    {
        $this->testUsesGivenModule(null, function (ViewTemplateRendererModules $modules) {
            $this->assertNotNull($modules->getEventDispatcher());
        });
    }

    public function testMakesTemplateCompiler(): void
    {
        $this->testUsesGivenModule(null, function (ViewTemplateRendererModules $modules) {
            $this->assertNotNull($modules->getTemplateCompiler());
        });
    }

    public function testMakesCodeExecutor(): void
    {
        $this->testUsesGivenModule(null, function (ViewTemplateRendererModules $modules) {
            $this->assertNotNull($modules->getCodeExecutor());
        });
    }

    //// testUsesGiven

    public function testUsesGivenTemplateRenderer(): void
    {
        // given
        $module = Mockery::mock(TemplateRendererInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewTemplateRendererModules $modules) use ($module) {
                $modules->setTemplateRenderer($module);
            },
            function (ViewTemplateRendererModules $modules) {
                $modules->getTemplateRenderer()->renderTemplate('', []);
            }
        );

        // then
        $module->shouldReceive('renderTemplate')
            ->once()
            ->andReturn('');

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenEventDispatcher(): void
    {
        // given
        $module = Mockery::mock(EventDispatcherInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewTemplateRendererModules $modules) use ($module) {
                $modules->setEventDispatcher($module);
            },
            function (ViewTemplateRendererModules $modules) {
                $modules->getEventDispatcher()->registerEventDetails('test', []);
            }
        );

        // then
        $module->shouldReceive('registerEventDetails')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenTemplateCompiler(): void
    {
        // given
        $module = Mockery::mock(TemplateCompilerInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewTemplateRendererModules $modules) use ($module) {
                $modules->setTemplateCompiler($module);
            },
            function (ViewTemplateRendererModules $modules) {
                $modules->getTemplateCompiler()->compileTemplate('test');
            }
        );

        // then
        $module->shouldReceive('compileTemplate')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenCodeExecutor(): void
    {
        // given
        $module = Mockery::mock(CodeRunnerInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewTemplateRendererModules $modules) use ($module) {
                $modules->setCodeExecutor($module);
            },
            function (ViewTemplateRendererModules $modules) {
                $modules->getCodeExecutor()->runCode('test');
            }
        );

        // then
        $module->shouldReceive('runCode')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    /**
     * We use a wrapper, case given objects are often decorated (wrapped),
     * so we can't compare the response by the plain comparison.
     *
     * @param callable(ViewTemplateRendererModules $modules):void $setModuleCallback
     * @param callable(ViewTemplateRendererModules $modules):void $callModuleCallback
     */
    protected function testUsesGivenModule(?callable $setModuleCallback, callable $callModuleCallback): void
    {
        // given
        $modules = new ViewTemplateRendererModules();
        $config = new ViewTemplateRendererConfig();

        if (null !== $setModuleCallback) {
            $setModuleCallback($modules);
        }

        $config->setModules($modules);

        // when
        $makeRenderer = fn()=> new ViewTemplateRenderer($config);

        // then
        $callModuleCallback($makeRenderer()->getModules());
    }
}
