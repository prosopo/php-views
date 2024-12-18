<?php

declare(strict_types=1);

namespace Tests\Unit\CodeExecutor;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\PrivateClasses\CodeRunner\CodeRunnerWithGlobalArguments;

class CodeRunnerWithGlobalArgumentsTest extends TestCase
{
    public function testMergesGlobalAndProvidedArgumentsBeforeExecutingCode(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeRunnerInterface::class);
        $executor = new CodeRunnerWithGlobalArguments($codeExecutor, ['globalKey' => 'globalValue']);

        // when
        $executeCode = fn() => $executor->runCode('sample code', ['key' => 'value']);

        // then
        $codeExecutor->shouldReceive('runCode')
            ->once()
            ->with('sample code', [
                'globalKey' => 'globalValue',
                'key' => 'value',
            ]);

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }

    public function testUsesOnlyGlobalArgumentsWhenNoAdditionalArgumentsAreProvided(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeRunnerInterface::class);
        $executor = new CodeRunnerWithGlobalArguments($codeExecutor, ['globalKey' => 'globalValue']);

        // when
        $executeCode = fn() => $executor->runCode('sample code', []);

        // then
        $codeExecutor->shouldReceive('runCode')
            ->once()
            ->with('sample code', [
                'globalKey' => 'globalValue',
            ]);

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }

    public function testOverridesGlobalArgumentsWithProvidedArgumentsIfKeysOverlap(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeRunnerInterface::class);
        $executor = new CodeRunnerWithGlobalArguments($codeExecutor, ['key' => 'globalValue']);

        // when
        $executeCode = fn() => $executor->runCode('sample code', ['key' => 'providedValue']);

        // then
        $codeExecutor->shouldReceive('runCode')
            ->once()
            ->with('sample code', [
                'key' => 'providedValue',
            ]);

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
