<?php

namespace Twigger\Tests\Unit\Translate\Locale\Strategies;

use Illuminate\Http\Request;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\Strategies\CookieDetectionStrategy;

class CookieDetectionStrategyTest extends TestCase
{

    /** @test */
    public function getCurrentLocale_returns_the_value_of_the_cookie_key(){
        $request = $this->prophesize(Request::class);
        $request->cookie('test_lang', null)->shouldBeCalled()->willReturn('en_GB');
        $CookieDetectionStrategy = new CookieDetectionStrategy($request->reveal(), 'test_lang');

        $this->assertEquals('en_GB', $CookieDetectionStrategy->detect());
    }

    /** @test */
    public function getCurrentLocale_returns_null_if_the_cookie_is_not_set(){
        $request = $this->prophesize(Request::class);
        $request->cookie('test_lang', null)->shouldBeCalled()->willReturn(null);
        $CookieDetectionStrategy = new CookieDetectionStrategy($request->reveal(), 'test_lang');

        $this->assertNull($CookieDetectionStrategy->detect());
    }

}
