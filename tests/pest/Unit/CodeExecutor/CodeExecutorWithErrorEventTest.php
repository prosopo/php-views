<?php

declare(strict_types=1);

namespace Tests\Unit\CodeExecutor;

use Error;
use Exception;
use Mockery;
use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\PrivateClasses\CodeExecutor\CodeExecutorWithErrorEvent;
use Tests\TestCase;
use Throwable;

class CodeExecutorWithErrorEventTest extends TestCase
{
    public function testNotCallsDispatcherWithoutReason(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $contestant = new CodeExecutorWithErrorEvent(
            $codeExecutor,
            $eventDispatcher,
            'error'
        );

        // when
        $executeCode = fn() => $contestant->executeCode('correct code', ['var' => 1]);

        // then
        $codeExecutor->shouldReceive('executeCode')
            ->once();
        $eventDispatcher->shouldNotReceive('dispatchEvent');

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }

    public function testCallsDispatcherOnError(): void
    {
        $this->testCallsDispatcher(new Error());
    }

    public function testCallsDispatcherOnException(): void
    {
        $this->testCallsDispatcher(new Exception());
    }

    protected function testCallsDispatcher(Throwable $error): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeExecutorInterface::class);
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $contestant = new CodeExecutorWithErrorEvent(
            $codeExecutor,
            $eventDispatcher,
            'errorEventName'
        );

        // when
        $executeCode = fn() => $contestant->executeCode('wrong code', ['var' => 1]);

        // then
         $codeExecutor->shouldReceive('executeCode')
            ->once()
            ->andThrow($error);

         $eventDispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with(
                'errorEventName',
                [
                    'arguments' => [
                        'var' => 1,
                    ],
                    'code' => 'wrong code',
                    'error' => $error,
                ]
            );

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
