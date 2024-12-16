<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNameProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\View\ViewNamespace;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewNamespaceModules;
use ReflectionProperty;
use stdClass;

class ViewNamespaceTest extends TestCase
{
    //// testMakes

    public function testMakesObjectReader()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getTemplateRenderer());
            }
        );
    }

    public function testMakesPropertyValueProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getPropertyValueProvider());
            }
        );
    }

    public function testMakesObjectPropertyWriter()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getObjectPropertyWriter());
            }
        );
    }

    public function testMakesModelFactory()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelFactory());
            }
        );
    }

    public function testMakesModelTemplateProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelTemplateProvider());
            }
        );
    }

    public function testMakesModelRenderer()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelRenderer());
            }
        );
    }

    public function testMakesEventDispatcher()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getEventDispatcher());
            }
        );
    }

    public function testMakesModelNameProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelNameProvider());
            }
        );
    }

    public function testMakesModelNamespaceProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelNamespaceProvider());
            }
        );
    }

    //// testUsesGiven

    public function testUsesGivenTemplateRendererReader(): void
    {
        // given
        $module = Mockery::mock(TemplateRendererInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setTemplateRenderer($module);
            },
            function (ViewNamespaceModules $modules) {
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

    public function testUsesGivenObjectReader(): void
    {
        // given
        $module = Mockery::mock(ObjectReaderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setObjectReader($module);
            },
            function (ViewNamespaceModules $modules) {
                $instance = Mockery::mock(StdClass::class);

                 $modules->getObjectReader()->getObjectVariables($instance);
            }
        );

        // then
        $module->shouldReceive('getObjectVariables')
            ->once()
            ->andReturn([]);

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenPropertyValueProvider(): void
    {
        // given
        $module = Mockery::mock(PropertyValueProviderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setPropertyValueProvider($module);
            },
            function (ViewNamespaceModules $modules) {
                $reflectionProperty = Mockery::mock(ReflectionProperty::class);

                $modules->getPropertyValueProvider()->getPropertyValue($reflectionProperty);
            }
        );

        // then
        $module->shouldReceive('supportsProperty')
            ->andReturn(true);
        $module->shouldReceive('getPropertyValue')
            ->once()
            ->andReturn('');

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenObjectPropertyWriter(): void
    {
        // given
        $module = Mockery::mock(ObjectPropertyWriterInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setObjectPropertyWriter($module);
            },
            function (ViewNamespaceModules $modules) {
                $instance = Mockery::mock(StdClass::class);

                $modules->getObjectPropertyWriter()->setObjectPropertyValues($instance);
            }
        );

        // then
        $module->shouldReceive('setObjectPropertyValues')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelFactory(): void
    {
        // given
        $module = Mockery::mock(ModelFactoryInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelFactory($module);
            },
            function (ViewNamespaceModules $modules) {
                $modules->getModelFactory()->makeModel('test');
            }
        );

        // then
        $module->shouldReceive('makeModel')
            ->once()
             ->andReturn(null);

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelTemplateProvider(): void
    {
        // given
        $module = Mockery::mock(ModelTemplateProviderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelTemplateProvider($module);
            },
            function (ViewNamespaceModules $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelTemplateProvider()->getModelTemplate($model);
            }
        );

        // then
        $module->shouldReceive('getModelTemplate')
            ->once()
            ->andReturn('');

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelRenderer(): void
    {
        // given
        $module = Mockery::mock(ModelRendererInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelRenderer($module);
            },
            function (ViewNamespaceModules $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelRenderer()->renderModel($model);
            }
        );

        // then
        $module->shouldReceive('renderModel')
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setEventDispatcher($module);
            },
            function (ViewNamespaceModules $modules) {
                $modules->getEventDispatcher()->attachEventDetails('test', []);
            }
        );

        // then
        $module->shouldReceive('attachEventDetails')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelNameProvider(): void
    {
        // given
        $module = Mockery::mock(ModelNameProviderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelNameProvider($module);
            },
            function (ViewNamespaceModules $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelNameProvider()->getModelName($model);
            }
        );

        // then
        $module->shouldReceive('getModelName')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelNamespaceProvider(): void
    {
        // given
        $module = Mockery::mock(ModelNamespaceProviderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelNamespaceProvider($module);
            },
            function (ViewNamespaceModules $modules) {
                $modules->getModelNamespaceProvider()->getModelNamespace('test');
            }
        );

        // then
        $module->shouldReceive('getModelNamespace')
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
     * @param callable(ViewNamespaceModules $modules):void $setModuleCallback
     * @param callable(ViewNamespaceModules $modules):void $callModuleCallback
     */
    protected function testUsesGivenModule(?callable $setModuleCallback, callable $callModuleCallback): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $modelRendererMock = Mockery::mock(ModelRendererInterface::class);

        $modules = new ViewNamespaceModules($templateRendererMock);
        $config = new ViewNamespaceConfig($templateRendererMock);

        if (null !== $setModuleCallback) {
            $setModuleCallback($modules);
        }

        $config->setModules($modules);

        // when
        $makeNamespace = fn()=> new ViewNamespace('', $config, $modelFactoryMock, $modelRendererMock);

        // then
        $callModuleCallback($makeNamespace()->getModules());
    }
}
