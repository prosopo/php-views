<?php

declare(strict_types=1);

namespace Tests\Unit\ObjectProperty;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\PrivateClasses\Object\PropertyValueProviderForModels;
use Prosopo\Views\View;

class InstancePropertyProviderTest extends TestCase
{
    public function testSupportsReturnsTrueForValidViewClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($viewFactory);
        $type = new class (Mockery::mock(TemplateProviderInterface::class)) extends View{
        };

        // when
        $isSupported = $provider->supportsProperty(get_class($type));

        // then
        $this->assertTrue($isSupported);
    }

    public function testSupportsReturnsFalseForInvalidClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($viewFactory);
        $type = new class {
        };

        // when
        $isSupported = $provider->supportsProperty(get_class($type));

        // then
        $this->assertFalse($isSupported);
    }

    public function testGetValueReturnsViewForValidViewClass(): void
    {
        // given
        $viewFactory = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($viewFactory);
        $viewInstance = Mockery::mock(View::class);
        $type =  new class (Mockery::mock(TemplateProviderInterface::class)) extends View{
        };

        // when
        $getValue = fn()=> $provider->getPropertyValue(get_class($type));

        // then
        $viewFactory->shouldReceive('makeModel')
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
        $viewFactory = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($viewFactory);
        $type = new class {
        };

        // when
        $result = $provider->getPropertyValue(get_class($type));

        // then
        $this->assertNull($result);
    }
}
