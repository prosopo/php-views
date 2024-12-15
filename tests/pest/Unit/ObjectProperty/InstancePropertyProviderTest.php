<?php

declare(strict_types=1);

namespace Tests\Unit\ObjectProperty;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\InstancePropertyProvider;
use Prosopo\Views\View;

class InstancePropertyProviderTest extends TestCase
{
    public function testSupportsReturnsTrueForValidViewClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ViewFactoryInterface::class);
        $provider = new InstancePropertyProvider($viewFactory);
        $type = new class (Mockery::mock(TemplateProviderInterface::class)) extends View{
        };

        // when
        $isSupported = $provider->supports(get_class($type));

        // then
        $this->assertTrue($isSupported);
    }

    public function testSupportsReturnsFalseForInvalidClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ViewFactoryInterface::class);
        $provider = new InstancePropertyProvider($viewFactory);
        $type = new class {
        };

        // when
        $isSupported = $provider->supports(get_class($type));

        // then
        $this->assertFalse($isSupported);
    }

    public function testGetValueReturnsViewForValidViewClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ViewFactoryInterface::class);
        $provider = new InstancePropertyProvider($viewFactory);
        $viewInstance = Mockery::mock(View::class);
        $type =  new class (Mockery::mock(TemplateProviderInterface::class)) extends View{
        };

        // when
        $getValue = fn()=> $provider->getValue(get_class($type));

        // then
        $viewFactory->shouldReceive('makeView')
            ->once()
            ->with(get_class($type))
            ->andReturn($viewInstance);

        $this->assertSame($viewInstance, $getValue());

        // apply
        Mockery::close();
    }

    public function testGetValueReturnsNullForInvalidClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ViewFactoryInterface::class);
        $provider = new InstancePropertyProvider($viewFactory);
        $type = new class {
        };

        // when
        $result = $provider->getValue(get_class($type));

        // then
        $this->assertNull($result);
    }
}
