<?php

namespace Twigger\Tests\Translate\Unit\Locale\Strategies;

use Illuminate\Http\Request;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\Strategies\BodyDetectionStrategy;

class BodyDetectionStrategyTest extends TestCase
{

    /** @test */
    public function getCurrentLocale_returns_the_value_of_the_Body_key(){
        $request = $this->prophesize(Request::class);
        $request->input('test_lang_input', null)->shouldBeCalled()->willReturn('en_GB');
        $BodyDetectionStrategy = new BodyDetectionStrategy($request->reveal(), 'test_lang_input');

        $this->assertEquals('en_GB', $BodyDetectionStrategy->detect());
    }

    /** @test */
    public function getCurrentLocale_returns_null_if_the_Body_is_not_set(){
        $request = $this->prophesize(Request::class);
        $request->input('test_lang_input', null)->shouldBeCalled()->willReturn(null);
        $BodyDetectionStrategy = new BodyDetectionStrategy($request->reveal(), 'test_lang_input');

        $this->assertNull($BodyDetectionStrategy->detect());
    }

}
