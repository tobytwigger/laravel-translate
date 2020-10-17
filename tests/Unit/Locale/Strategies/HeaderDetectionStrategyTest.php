<?php

namespace Twigger\Tests\Translate\Unit\Locale\Strategies;

use Illuminate\Http\Request;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\Strategies\HeaderDetectionStrategy;

class HeaderDetectionStrategyTest extends TestCase
{

    /** @test */
    public function getCurrentLocale_returns_the_request_preferred_language_from_the_array(){

        $request = $this->prophesize(Request::class);
        $request->getPreferredLanguage(['en_GB', 'en_US'])->shouldBeCalled()->willReturn('en_GB');
        $headerDetectionStrategy = new HeaderDetectionStrategy($request->reveal(), ['en_GB', 'en_US']);

        $this->assertEquals('en_GB', $headerDetectionStrategy->detect());
    }

    /** @test */
    public function the_languages_array_is_sorted_before_use(){
        $request = $this->prophesize(Request::class);
        $request->getPreferredLanguage(['en_GB', 'en_US'])->shouldBeCalled()->willReturn('en_GB');
        $headerDetectionStrategy = new HeaderDetectionStrategy($request->reveal(), ['en_US', 'en_GB']);

        $this->assertEquals('en_GB', $headerDetectionStrategy->detect());
    }

}
