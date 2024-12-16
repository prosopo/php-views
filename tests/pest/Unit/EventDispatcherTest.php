<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prosopo\Views\PrivateClasses\EventDispatcher;

class EventDispatcherTest extends TestCase
{
    public function testAddEventListenerAndDispatchEvent(): void
    {
        // given
        $dispatcher = new EventDispatcher();
        $listenerCalled = false;
        $receivedDetails = null;
        $listener = function (array $details) use (&$listenerCalled, &$receivedDetails) {
            $listenerCalled = true;
            $receivedDetails = $details;
        };

        // when
        $dispatcher->addEventListener('testEvent', $listener);
        $dispatcher->dispatchEvent('testEvent', ['key' => 'value']);

        // then
        $this->assertTrue($listenerCalled);
        $this->assertSame(['key' => 'value'], $receivedDetails);
    }

    public function testAddMultipleEventListeners(): void
    {
        // given
        $dispatcher = new EventDispatcher();

        $listener1Called = false;
        $listener1Details = null;
        $listener1 = function (array $details) use (&$listener1Called, &$listener1Details) {
            $listener1Called = true;
            $listener1Details = $details;
        };

        $listener2Called = false;
        $listener2Details = null;
        $listener2 = function (array $details) use (&$listener2Called, &$listener2Details) {
            $listener2Called = true;
            $listener2Details = $details;
        };

        // when
        $dispatcher->addEventListener('testEvent', $listener1);
        $dispatcher->addEventListener('testEvent', $listener2);
        $dispatcher->dispatchEvent('testEvent', ['key' => 'value']);

        // then
        $this->assertTrue($listener1Called);
        $this->assertSame(['key' => 'value'], $listener1Details);

        $this->assertTrue($listener2Called);
        $this->assertSame(['key' => 'value'], $listener2Details);
    }

    public function testRemoveEventListener(): void
    {
        // given
        $dispatcher = new EventDispatcher();
        $listenerCalled = false;

        $listener = function (array $details) use (&$listenerCalled) {
            $listenerCalled = true;
        };

        // when
        $dispatcher->addEventListener('testEvent', $listener);
        $dispatcher->removeEventListener('testEvent', $listener);
        $dispatcher->dispatchEvent('testEvent', ['key' => 'value']);

        // then
        $this->assertFalse($listenerCalled);
    }

    public function testAttachEventDetails(): void
    {
        // given
        $dispatcher = new EventDispatcher();
        $listenerCalled = false;
        $receivedDetails = null;
        $listener = function (array $details) use (&$listenerCalled, &$receivedDetails) {
            $listenerCalled = true;
            $receivedDetails = $details;
        };

        // when
        $dispatcher->attachEventDetails('testEvent', ['attachedKey' => 'attachedValue']);
        $dispatcher->addEventListener('testEvent', $listener);
        $dispatcher->dispatchEvent('testEvent', ['key' => 'value']);

        // then
        $this->assertTrue($listenerCalled);
        $this->assertSame(
            ['attachedKey' => 'attachedValue', 'key' => 'value'],
            $receivedDetails
        );
    }

    public function testDetachEventDetails(): void
    {
        // given
        $dispatcher = new EventDispatcher();

        $listenerCalled = false;
        $receivedDetails = null;
        $listener = function (array $details) use (&$listenerCalled, &$receivedDetails) {
            $listenerCalled = true;
            $receivedDetails = $details;
        };

        // when
        $dispatcher->attachEventDetails('testEvent', ['key1' => 'value1', 'key2' => 'value2']);
        $dispatcher->detachEventDetails('testEvent', ['key2' => 'value2']);
        $dispatcher->addEventListener('testEvent', $listener);
        $dispatcher->dispatchEvent('testEvent', ['key3' => 'value3']);

        // then
        $this->assertTrue($listenerCalled);
        $this->assertSame(
            ['key1' => 'value1', 'key3' => 'value3'],
            $receivedDetails
        );
    }

    public function testDispatchEventWithNoListeners(): void
    {
        // given
        $dispatcher = new EventDispatcher();

        // when
        $dispatcher->dispatchEvent('testEvent', ['key' => 'value']);

        // then
        // No assertions needed; just ensure no exceptions are thrown.
        $this->assertTrue(true);
    }

    public function testEventListenersAreIsolatedPerEvent(): void
    {
        // given
        $dispatcher = new EventDispatcher();

        $listenerCalled = false;
        $listener = function (array $details) use (&$listenerCalled) {
            $listenerCalled = true;
        };

        // when
        $dispatcher->addEventListener('eventA', $listener);
        $dispatcher->dispatchEvent('eventB', ['key' => 'value']);

        // then
        $this->assertFalse($listenerCalled);
    }

    public function testEventDetailsAreIsolatedPerEvent(): void
    {
        // given
        $dispatcher = new EventDispatcher();

        $listenerCalled = false;
        $receivedDetails = null;
        $listener = function (array $details) use (&$listenerCalled, &$receivedDetails) {
            $listenerCalled = true;
            $receivedDetails = $details;
        };

        // when
        $dispatcher->attachEventDetails('eventA', ['attachedKey' => 'attachedValue']);
        $dispatcher->addEventListener('eventB', $listener);
        $dispatcher->dispatchEvent('eventB', ['key' => 'value']);

        // then
        $this->assertTrue($listenerCalled);
        $this->assertSame(['key' => 'value'], $receivedDetails);
    }

    public function testEventListenersAreNotCalledAfterRemoval(): void
    {
        // given
        $dispatcher = new EventDispatcher();

        $listener1Called = false;
        $listener1 = function (array $details) use (&$listener1Called) {
            $listener1Called = true;
        };

        $listener2Called = false;
        $listener2 = function (array $details) use (&$listener2Called) {
            $listener2Called = true;
        };

        // when
        $dispatcher->addEventListener('testEvent', $listener1);
        $dispatcher->removeEventListener('testEvent', $listener1);
        $dispatcher->addEventListener('testEvent', $listener2);
        $dispatcher->dispatchEvent('testEvent', ['key' => 'value']);

        // then
        $this->assertFalse($listener1Called);
        $this->assertTrue($listener2Called);
    }
}
