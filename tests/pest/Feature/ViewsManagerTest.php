<?php

declare(strict_types=1);

namespace Tests\Feature;

use org\bovigo\vfs\vfsStream;
use ParseError;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\BaseTemplateModel;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\View\ViewTemplateRendererConfig;
use Prosopo\Views\ViewsManager;

class ViewsManagerTest extends TestCase
{
    //// renderModel

    public function testRenderModelThatImplementsInterface(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
            [
                'defaultValue' => '"Hello World!"',
                'name' => 'message',
                'type' => 'string',
                'visibility' => 'private',
            ]
            ],
            false
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderModelThatImplementInterfaceIgnoresPublicProperties(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}{{ $data }}']);
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'defaultValue' => '"Hello World!"',
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'private',
                ],
                [
                    'defaultValue' => '"Internal"',
                    'name' => 'data',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            false,
            ['data',]
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderModelThatExtendsBaseClass(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->createModel($modelClass);
        $model->message = 'Hello World!';

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderCallsSetupCallback(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';

        // then
        $this->assertSame('Hello World!', $views->renderModel($modelClass, function ($model) {
            $model->message = 'Hello World!';
        }));
    }

    public function testRenderModelPassesStringToTemplateRendererWhenFileBasedModeIsDisabled(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeConfig = new ViewTemplateRendererConfig();
        $bladeConfig->setFileBasedTemplates(false);
        $bladeRenderer = new ViewTemplateRenderer($bladeConfig);
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php')
            ->setFileBasedTemplates(false);
        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'defaultValue' => '"Hello World!"',
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'private',
                ]
            ],
            false
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderCallsTemplateErrorHandler(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '@if($message)wrong template']);
        $bladeRenderer = new ViewTemplateRenderer();
        $receivedEventDetails = null;
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php')
            ->setTemplateErrorHandler(function ($errorDetails) use (&$receivedEventDetails) {
                $receivedEventDetails = $errorDetails;
            });
        $namespaceConfig
            ->getModules()
            ->setEventDispatcher($bladeRenderer->getModules()->getEventDispatcher());
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);
        $modelClass = $modelNamespace . '\\FirstModel';
        $rendered = $views->renderModel($modelClass, (function ($model) {
            $model->message = 'some data';
        }));

        // then
        $this->assertSame('', $rendered);
        $this->assertSame(['modelClass','template','arguments','code','error',], array_keys($receivedEventDetails));
        $this->assertSame($modelClass, $receivedEventDetails['modelClass']);
        $this->assertSame(vfsStream::url('templates/first-model.blade.php'), $receivedEventDetails['template']);
        $this->assertSame('some data', $receivedEventDetails['arguments']['message']);
        $this->assertSame('<?php if( $message ): ?>wrong template', $receivedEventDetails['code']);
        $this->assertSame(ParseError::class, get_class($receivedEventDetails['error']));
    }

    public function testRenderNotCallTemplateErrorHandlerWithoutReason(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '@if($message)good template@endif']);
        $bladeRenderer = new ViewTemplateRenderer();
        $receivedEventDetails = null;
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php')
            ->setTemplateErrorHandler(function ($errorDetails) use (&$receivedEventDetails) {
                $receivedEventDetails = $errorDetails;
            });
        $namespaceConfig
            ->getModules()
            ->setEventDispatcher($bladeRenderer->getModules()->getEventDispatcher());
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);
        $modelClass = $modelNamespace . '\\FirstModel';
        $rendered = $views->renderModel($modelClass, (function ($model) {
            $model->message = 'some data';
        }));

        // then
        $this->assertSame('good template', $rendered);
        $this->assertNull($receivedEventDetails);
    }

    public function testRenderSupportsInnerNamespaces(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'inner' => [
                'first-model.blade.php' => '{{ $message }}'
            ],
        ]);
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $innerModelNamespace = $this->defineRealModelClass(
            __METHOD__ . '\Inner',
            'FirstModel',
            [
                [
                    'defaultValue' => '"Hello World!"',
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'private',
                ]
            ],
            false
        );
        $namespace = str_replace('\inner', '', $innerModelNamespace);
        $views = new ViewsManager();

        // when
        $views->registerNamespace($namespace, $namespaceConfig);

        $modelClass = $innerModelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderSupportsDifferentNamespaces(): void
    {
        // given
        vfsStream::setup('top', null, [
           'folder1' => ['first-model.blade.php' => '{{ $message }}'],
            'folder2' => ['second-model.blade.php' => '{{ $result }}'],
        ]);
        $bladeRenderer = new ViewTemplateRenderer();
        $firstNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder1'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder2'))
            ->setTemplateFileExtension('.blade.php');
        $firstNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'defaultValue' => '"Hello World!"',
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'private',
                ]
            ],
            false
        );
        $secondNamespace = $this->defineRealModelClass(
            __METHOD__ . '__second',
            'SecondModel',
            [
                [
                    'defaultValue' => '"Looks good!"',
                    'name' => 'result',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($firstNamespace, $firstNamespaceConfig);
        $views->registerNamespace($secondNamespace, $secondNamespaceConfig);

        $firstModelClass = $firstNamespace . '\\FirstModel';
        $firstModel = new $firstModelClass();
        $secondModelClass = $secondNamespace . '\\SecondModel';

        // then
        $this->assertSame('Hello World!', $views->renderModel($firstModel));
        $this->assertSame('Looks good!', $views->renderModel($secondModelClass));
    }

    public function testRenderSupportsCustomCompiler(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'pure.php' => '<?php echo $message; $new = " and again: ".$message; echo $new; ?>',
        ]);
        $compilerStub = new class implements TemplateCompilerInterface{
            public function compileTemplate(string $template): string
            {
                return $template;
            }
        };
        $viewTemplateRendererConfig = new ViewTemplateRendererConfig();
        $viewTemplateRendererConfig->getModules()
            ->setTemplateCompiler($compilerStub);
        $viewTemplateRenderer = new ViewTemplateRenderer($viewTemplateRendererConfig);
        $views = new ViewsManager();
        $viewNamespaceConfig = new ViewNamespaceConfig($viewTemplateRenderer);
        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'Pure',
            [
                [
                    'name' => 'message',
                    'visibility' => 'public',
                ]
            ],
            false
        );

        // when
        $viewNamespaceConfig
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.php');

        $views->registerNamespace($modelNamespace, $viewNamespaceConfig);

        $modelClass = $modelNamespace . '\\Pure';
        $model = new $modelClass();
        $model->message = 'Hello World!';

        // then
        $this->assertSame('Hello World! and again: Hello World!', $views->renderModel($model));
    }

    public function testRenderPassesInnerModelsAsObjects(): void
    {
        // given
        vfsStream::setup('top', null, [
            'folder1' => ['top-model.blade.php' => 'Hey {{ true === is_object($inner)? "inner object": "string" }}'],
            'folder2' => [ 'inner-model.blade.php' => 'inner!'],
        ]);
        $bladeRenderer = new ViewTemplateRenderer();
        $firstNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder1'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder2'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespace = $this->defineRealModelClass(
            __METHOD__ . '__second',
            'InnerModel',
            [],
            false
        );
        $firstNamespace = $this->defineRealModelClass(
            __METHOD__,
            'TopModel',
            [
                [
                    'name' => 'inner',
                    'visibility' => 'public',
                ]
            ],
            false
        );
        $viewsManager = new ViewsManager();

        // when
        $viewsManager->registerNamespace($firstNamespace, $firstNamespaceConfig);
        $viewsManager->registerNamespace($secondNamespace, $secondNamespaceConfig);

        $innerModelClass = $secondNamespace . '\\InnerModel';
        $topModelClass = $firstNamespace . '\\TopModel';
        $topModel = new $topModelClass();
        $topModel->inner = new $innerModelClass();

        // then
        $this->assertSame('Hey inner object', $viewsManager->renderModel($topModel));
    }

    public function testRenderSupportsInnerModelPrintWhenModelExtendsTheBaseClass(): void
    {
        // given
        vfsStream::setup('top', null, [
            'folder1' => ['top-model.blade.php' => 'Hey {{ $inner }}'],
            'folder2' => [ 'inner-model.blade.php' => 'inner!'],
        ]);
        $bladeRenderer = new ViewTemplateRenderer();
        $firstNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder1'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder2'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespace = $this->defineRealModelClass(
            __METHOD__ . '__second',
            'InnerModel',
            [],
            true
        );
        $innerModelClass = $secondNamespace . '\\InnerModel';
        $firstNamespace = $this->defineRealModelClass(
            __METHOD__,
            'TopModel',
            [
                [
                    'name' => 'inner',
                    'type' => '\\' . $innerModelClass,
                    'visibility' => 'public',
                ]
            ],
            true
        );
        $viewsManager = new ViewsManager();

        // when
        $viewsManager->registerNamespace($firstNamespace, $firstNamespaceConfig);
        $viewsManager->registerNamespace($secondNamespace, $secondNamespaceConfig);

        $topModelClass = $firstNamespace . '\\TopModel';
        $topModel = $viewsManager->createModel($topModelClass);
        $topModel->inner = $viewsManager->createModel($innerModelClass);

        // then
        $this->assertSame('Hey inner!', $viewsManager->renderModel($topModel));
    }

    public function testRenderPassesInnerModelsAsStringsWhenFlagIsSet(): void
    {
        // given
        vfsStream::setup('top', null, [
            'folder1' => ['top-model.blade.php' => 'Hey {!! $inner !!}'],
            'folder2' => [ 'inner-model.blade.php' => 'inner!'],
        ]);
        $bladeRenderer = new ViewTemplateRenderer();
        $firstNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder1'))
            ->setTemplateFileExtension('.blade.php')
            ->setModelsAsStringsInTemplates(true);
        $secondNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder2'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespace = $this->defineRealModelClass(
            __METHOD__ . '__second',
            'InnerModel',
            [],
            false
        );
        $firstNamespace = $this->defineRealModelClass(
            __METHOD__,
            'TopModel',
            [
                [
                    'name' => 'inner',
                    'visibility' => 'public',
                ]
            ],
            false
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($firstNamespace, $firstNamespaceConfig);
        $views->registerNamespace($secondNamespace, $secondNamespaceConfig);

        $innerModelClass = $secondNamespace . '\\InnerModel';
        $topModelClass = $firstNamespace . '\\TopModel';
        $topModel = new $topModelClass();
        $topModel->inner = new $innerModelClass();

        // then
        $this->assertSame('Hey inner!', $views->renderModel($topModel));
    }

    public function testRenderPassesInnerModelsFromDifferentNamespaces(): void
    {
        // given
        vfsStream::setup('top', null, [
            'folder1' => ['top-model.blade.php' => 'Hey {!! $inner !!}'],
            'folder2' => [ 'inner-model.blade.php' => 'inner!'],
        ]);
        $bladeRenderer = new ViewTemplateRenderer();
        $firstNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder1'))
            ->setTemplateFileExtension('.blade.php')
            ->setModelsAsStringsInTemplates(true);
        $secondNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('top/folder2'))
            ->setTemplateFileExtension('.blade.php');
        $secondNamespace = $this->defineRealModelClass(
            __METHOD__ . '__second',
            'InnerModel',
            [],
            false
        );
        $firstNamespace = $this->defineRealModelClass(
            __METHOD__,
            'TopModel',
            [
                [
                    'name' => 'inner',
                    'visibility' => 'public',
                ]
            ],
            false
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($firstNamespace, $firstNamespaceConfig);
        $views->registerNamespace($secondNamespace, $secondNamespaceConfig);

        $innerModelClass = $secondNamespace . '\\InnerModel';
        $topModelClass = $firstNamespace . '\\TopModel';
        $topModel = new $topModelClass();
        $topModel->inner = new $innerModelClass();

        // then
        $this->assertSame('Hey inner!', $views->renderModel($topModel));
    }

    /// makeModel

    public function testMakeModelThatExtendsBaseClass(): void
    {
        // given
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [],
            true
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->createModel($modelClass);

        // then
        $this->assertSame($modelClass, get_class($model));
    }

    public function testMakeModelImplementsInterface(): void
    {
        // given
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            false
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->createModel($modelClass);

        // then
        $this->assertSame($modelClass, get_class($model));
    }

    public function testMakeModelSetsDefaultsForModelsThatExtendBaseClass(): void
    {
        // given
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->createModel($modelClass);

        // then
        $this->assertSame('', $model->message);
    }

    public function testMakeModelNotSetDefaultsForModelsWithoutDefaultsInterface(): void
    {
        // given
        $bladeRenderer = new ViewTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new ViewsManager();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            false
        );

        // when
        $views->registerNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->createModel($modelClass);

        // then
        $this->assertFalse(isset($model->message));
    }

    public function testMakeModelSupportsDifferentNamespaces(): void
    {
        // given
        $bladeRenderer = new ViewTemplateRenderer();
        $firstNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $secondNamespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $firstNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [],
            false
        );
        $secondNamespace = $this->defineRealModelClass(
            __METHOD__ . '__second',
            'SecondModel',
            [],
            true
        );
        $views = new ViewsManager();

        // when
        $views->registerNamespace($firstNamespace, $firstNamespaceConfig);
        $views->registerNamespace($secondNamespace, $secondNamespaceConfig);

        $firstModelClass = $firstNamespace . '\\FirstModel';
        $secondModelClass = $secondNamespace . '\\SecondModel';

        // then
        $this->assertSame($firstModelClass, get_class($views->createModel($firstModelClass)));
        $this->assertSame($secondModelClass, get_class($views->createModel($secondModelClass)));
    }

    /**
     * @param array<int,array{visibility:string,name:string, type?:string, defaultValue?:mixed}> $properties
     * @param string[] $tempatePropertiesNamesToIgnore
     */
    protected function defineRealModelClass(
        string $method,
        string $className,
        array $properties,
        bool $extendsClass,
        array $tempatePropertiesNamesToIgnore = []
    ): string {
        $methodName = explode('::', $method);
        $methodName = $methodName[count($methodName) - 1];

        $namespace = '_views_test_' . strtolower($methodName);
        $classContent = $this->getClassProperties($properties);
        $extends = true === $extendsClass ?
            'extends \\' . BaseTemplateModel::class :
            'implements \\' . TemplateModelInterface::class;

        if (false === $extendsClass) {
            $classContent .= $this->makeTemplateArgumentsMethod($properties, $tempatePropertiesNamesToIgnore);
        }

        $code = sprintf('namespace %s; class %s %s { %s }', $namespace, $className, $extends, $classContent);

        eval($code);

        return $namespace;
    }

    /**
     * @param array<int,array{visibility:string,name:string, type?:string, defaultValue?:mixed}> $properties
     */
    protected function getClassProperties(array $properties): string
    {
        return array_reduce($properties, function ($classContent, $property) {
            $defaultValue = $property['defaultValue'] ?? null;
            $defaultValue = null !== $defaultValue ?
                sprintf(' = %s', $defaultValue) :
                '';

            return $classContent . sprintf(
                '%s %s $%s %s;',
                $property['visibility'],
                $property['type'] ?? '',
                $property['name'],
                $defaultValue
            );
        },
            '');
    }

    /**
     * @param array<string,string|null> $properties name => type|null
     * @param string[] $templatePropertiesNamesToIgnore
     */
    protected function makeTemplateArgumentsMethod(array $properties, array $templatePropertiesNamesToIgnore): string
    {
        $properties = array_filter($properties, function ($property) use ($templatePropertiesNamesToIgnore) {
            return false === in_array($property['name'], $templatePropertiesNamesToIgnore);
        });

        $arrayContent = array_reduce($properties, function ($arrayContent, $property) {
            return $arrayContent . sprintf(
                '"%s" => $this->%1$s,',
                $property['name']
            );
        }, '');

        return sprintf('function getTemplateArguments():array{return [%s];}', $arrayContent);
    }
}
