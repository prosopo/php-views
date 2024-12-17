<?php

declare(strict_types=1);

namespace Tests\Feature;

use org\bovigo\vfs\vfsStream;
use ParseError;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\TemplateTemplateModel;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\Views;

class ViewsTest extends TestCase
{
    //// renderModel

    public function testRenderModelThatImplementsInterface(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderModelThatImplementInterfaceIgnoresPublicProperties(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}{{ $data }}']);
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderModelThatExtendsBaseClass(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->makeModel($modelClass);
        $model->message = 'Hello World!';

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderCallsSetupCallback(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);

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
        $bladeConfig = new BladeRendererConfig();
        $bladeConfig->setIsFileBasedTemplate(false);
        $bladeRenderer = new BladeTemplateRenderer($bladeConfig);
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php')
            ->setIsFileBasedTemplate(false);
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
        $views = new Views();

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderCallsTemplateErrorHandler(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '@if($message)wrong template']);
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);
        $modelClass = $modelNamespace . '\\FirstModel';
        $rendered = $views->renderModel($modelClass, (function ($model) {
            $model->message = 'some data';
        }));

        // then
        $this->assertSame('', $rendered);
        $this->assertSame(['viewClass','template','arguments','code','error',], array_keys($receivedEventDetails));
        $this->assertSame($modelClass, $receivedEventDetails['viewClass']);
        $this->assertSame(vfsStream::url('templates/first-model.blade.php'), $receivedEventDetails['template']);
        $this->assertSame('some data', $receivedEventDetails['arguments']['message']);
        $this->assertSame('<?php if( $message ): ?>wrong template', $receivedEventDetails['code']);
        $this->assertSame(ParseError::class, get_class($receivedEventDetails['error']));
    }

    public function testRenderNotCallTemplateErrorHandlerWithoutReason(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '@if($message)good template@endif']);
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);
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
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

        // when
        $views->addNamespace($namespace, $namespaceConfig);

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
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

        // when
        $views->addNamespace($firstNamespace, $firstNamespaceConfig);
        $views->addNamespace($secondNamespace, $secondNamespaceConfig);

        $firstModelClass = $firstNamespace . '\\FirstModel';
        $firstModel = new $firstModelClass();
        $secondModelClass = $secondNamespace . '\\SecondModel';

        // then
        $this->assertSame('Hello World!', $views->renderModel($firstModel));
        $this->assertSame('Looks good!', $views->renderModel($secondModelClass));
    }

    public function testRenderIncludesInnerModel(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'inner-model.blade.php' => 'inner!',
            'top-model.blade.php' => 'Hey {!! $inner !!}',
        ]);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $this->defineRealModelClass(
            __METHOD__,
            'InnerModel',
            [],
            false
        );
        $modelNamespace = $this->defineRealModelClass(
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
        $views = new Views();

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $innerModelClass = $modelNamespace . '\\InnerModel';
        $topModelClass = $modelNamespace . '\\TopModel';
        $topModel = new $topModelClass();
        $topModel->inner = new $innerModelClass();

        // then
        $this->assertSame('Hey inner!', $views->renderModel($topModel));
    }

    public function testRenderIncludesInnerModelFromDifferentNamespace(): void
    {
        // fixme
    }

    public function testRenderSupportsCustomCompiler(): void
    {
        // fixme
    }

    /// makeModel

    public function testMakeModelThatExtendsBaseClass(): void
    {
        // given
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new Views();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [],
            true
        );

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->makeModel($modelClass);

        // then
        $this->assertSame($modelClass, get_class($model));
    }

    public function testMakeModelImplementsInterface(): void
    {
        // given
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->makeModel($modelClass);

        // then
        $this->assertSame($modelClass, get_class($model));
    }

    public function testMakeModelSetsDefaultsForModelsThatExtendBaseClass(): void
    {
        // given
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->makeModel($modelClass);

        // then
        $this->assertSame('', $model->message);
    }

    public function testMakeModelNotSetDefaultsForModelsWithoutDefaultsInterface(): void
    {
        // given
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer));
        $views = new Views();

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
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->makeModel($modelClass);

        // then
        $this->assertFalse(isset($model->message));
    }

    public function testMakeModelSupportsDifferentNamespaces(): void
    {
        // given
        $bladeRenderer = new BladeTemplateRenderer();
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
        $views = new Views();

        // when
        $views->addNamespace($firstNamespace, $firstNamespaceConfig);
        $views->addNamespace($secondNamespace, $secondNamespaceConfig);

        $firstModelClass = $firstNamespace . '\\FirstModel';
        $secondModelClass = $secondNamespace . '\\SecondModel';

        // then
        $this->assertSame($firstModelClass, get_class($views->makeModel($firstModelClass)));
        $this->assertSame($secondModelClass, get_class($views->makeModel($secondModelClass)));
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
            'extends \\' . TemplateTemplateModel::class :
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
