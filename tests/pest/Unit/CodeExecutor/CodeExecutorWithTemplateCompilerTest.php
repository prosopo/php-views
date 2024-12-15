<?php

declare(strict_types=1);

namespace Tests\Unit\CodeExecutor;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\PrivateClasses\CodeExecutor\CodeExecutorWithTemplateCompilation;

class CodeExecutorWithTemplateCompilationTest extends TestCase
{
    public function testExecutesCompiledTemplateCode(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $templateCompiler = Mockery::mock(TemplateCompilerInterface::class);
        $contestant = new CodeExecutorWithTemplateCompilation($codeExecutor, $templateCompiler);

        // when
        $executeCode = fn() => $contestant->executeCode('template code', ['arg1' => 'value1']);

        // then
        $templateCompiler->shouldReceive('compileTemplate')
            ->once()
            ->with('template code')
            ->andReturn('compiled code');

        $codeExecutor->shouldReceive('executeCode')
            ->once()
            ->with('compiled code', ['arg1' => 'value1']);

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }

    public function testHandlesEmptyTemplateCode(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $templateCompiler = Mockery::mock(TemplateCompilerInterface::class);
        $contestant = new CodeExecutorWithTemplateCompilation($codeExecutor, $templateCompiler);

        // when
        $executeCode = fn() => $contestant->executeCode('', []);

        // then
        $templateCompiler->shouldReceive('compileTemplate')
            ->once()
            ->with('')
            ->andReturn('');

        $codeExecutor->shouldReceive('executeCode')
            ->once()
            ->with('', []);

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }

    public function testHandlesArgumentsCorrectly(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $templateCompiler = Mockery::mock(TemplateCompilerInterface::class);
        $contestant = new CodeExecutorWithTemplateCompilation($codeExecutor, $templateCompiler);

        // when
        $executeCode = fn() => $contestant->executeCode('template code with args', ['key1' => 'value1', 'key2' => 'value2']);

        // then
        $templateCompiler->shouldReceive('compileTemplate')
            ->once()
            ->with('template code with args')
            ->andReturn('compiled code with args');

        $codeExecutor->shouldReceive('executeCode')
            ->once()
            ->with('compiled code with args', ['key1' => 'value1', 'key2' => 'value2']);

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
