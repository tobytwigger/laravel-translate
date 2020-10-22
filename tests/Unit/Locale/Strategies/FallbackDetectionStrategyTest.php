<?php

namespace Twigger\Tests\Translate\Unit\Locale\Strategies;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\Strategies\FallbackDetectionStrategy;

class FallbackDetectionStrategyTest extends TestCase
{

    /** @test */
    public function getCurrentLocale_returns_the_value_of_the_app_locale(){
        $app = $this->prophesize(Application::class);
        $app->getFallbackLocale()->willReturn('en_GB');

        $FallbackDetectionStrategy = new FallbackDetectionStrategy($app->reveal());

        $this->assertEquals('en_GB', $FallbackDetectionStrategy->detect());
    }

}
