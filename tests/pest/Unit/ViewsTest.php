<?php

declare(strict_types=1);

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Views;

class ViewsTest extends TestCase
{
    public function testMakeModelThrowsExceptionForMissingClass(): void
    {
        // given
        $views = new Views();

        // when
        $makeModel = fn()=> $views->createModel('MissingClass');

        // then
        $this->expectException(Exception::class);

        // apply
        $makeModel();
    }

    public function testMakeModelThrowsExceptionForClassThatNotImplementTemplateModelInterface(): void
    {
        // given
        $views = new Views();

        // when
        $makeModel = fn()=> $views->createModel(get_class($this));

        // then
        $this->expectException(Exception::class);

        // apply
        $makeModel();
    }

    public function testRenderThrowsExceptionForMissingClass(): void
    {
        // given
        $views = new Views();

        // when
        $makeModel = fn()=> $views->renderModel('MissingClass');

        // then
        $this->expectException(Exception::class);

        // apply
        $makeModel();
    }

    public function testRenderThrowsExceptionForClassThatNotImplementTemplateModelInterface(): void
    {
        // given
        $views = new Views();

        // when
        $makeModel = fn()=> $views->renderModel(get_class($this));

        // then
        $this->expectException(Exception::class);

        // apply
        $makeModel();
    }

    public function testRenderThrowsExceptionForInstanceThatNotImplementTemplateModelInterface(): void
    {
        // given
        $views = new Views();

        // when
        $makeModel = fn()=> $views->renderModel($this);

        // then
        $this->expectException(Exception::class);

        // apply
        $makeModel();
    }

    // the rest is in the /Feature/ViewsTest.php
}
