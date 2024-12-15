<?php

declare(strict_types=1);

namespace Tests\Unit\CodeExecutor;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\PrivateClasses\CodeExecutor\CodeExecutorWithGlobalArguments;

class CodeExecutorWithGlobalArgumentsTest extends TestCase
{
    public function testMergesGlobalAndProvidedArgumentsBeforeExecutingCode(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $executor = new CodeExecutorWithGlobalArguments($codeExecutor, ['globalKey' => 'globalValue']);

        // when
        $executeCode = fn() => $executor->executeCode('sample code', ['key' => 'value']);

        // then
        $codeExecutor->shouldReceive('executeCode')
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
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $executor = new CodeExecutorWithGlobalArguments($codeExecutor, ['globalKey' => 'globalValue']);

        // when
        $executeCode = fn() => $executor->executeCode('sample code', []);

        // then
        $codeExecutor->shouldReceive('executeCode')
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
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $executor = new CodeExecutorWithGlobalArguments($codeExecutor, ['key' => 'globalValue']);

        // when
        $executeCode = fn() => $executor->executeCode('sample code', ['key' => 'providedValue']);

        // then
        $codeExecutor->shouldReceive('executeCode')
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
