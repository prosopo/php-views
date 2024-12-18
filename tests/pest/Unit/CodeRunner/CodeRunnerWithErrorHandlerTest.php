<?php

declare(strict_types=1);

namespace Tests\Unit\CodeExecutor;

use Error;
use Exception;
use Mockery;
use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\PrivateClasses\CodeRunner\CodeRunnerWithErrorHandler;
use Tests\TestCase;
use Throwable;

class CodeRunnerWithErrorHandlerTest extends TestCase
{
    public function testNotCallsDispatcherWithoutReason(): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeRunnerInterface::class);
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $contestant = new CodeRunnerWithErrorHandler(
            $codeExecutor,
            $eventDispatcher,
            'error'
        );

        // when
        $executeCode = fn() => $contestant->runCode('correct code', ['var' => 1]);

        // then
        $codeExecutor->shouldReceive('runCode')
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

    public function testCallsDispatcherOnWarning(): void
    {
        $this->testCallsDispatcher(null, true);
    }

    protected function testCallsDispatcher(?Throwable $error, bool $isWarning = false): void
    {
        // given
        $codeExecutor = Mockery::mock(CodeRunnerInterface::class);
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $contestant = new CodeRunnerWithErrorHandler(
            $codeExecutor,
            $eventDispatcher,
            'errorEventName'
        );

        // when
        $executeCode = fn() => $contestant->runCode('wrong code', ['var' => 1]);

        // then
         $executorRule = $codeExecutor->shouldReceive('runCode')
            ->once();

        if (null !== $error) {
            $executorRule->andThrow($error);
        }

        if (true === $isWarning) {
            $executorRule->andReturnUsing(fn() => trigger_error('test', E_USER_WARNING));
        }

         $eventDispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with(
                'errorEventName',
                Mockery::on(function ($details) {
                    return isset($details['arguments'], $details['code'], $details['error'])
                        && $details['arguments'] === ['var' => 1]
                        && $details['code'] === 'wrong code'
                        && $details['error'] instanceof Throwable;
                })
            );

        // apply
        $executeCode();
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
