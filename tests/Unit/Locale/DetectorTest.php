<?php

namespace Twigger\Tests\Unit\Translate\Locale;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\DetectionStrategy;
use Twigger\Translate\Locale\Detector;

class DetectorTest extends TestCase
{

    /** @test */
    public function lang_calls_detect_on_the_underlying_strategy(){
        $detectionStrategy = $this->prophesize(DetectionStrategy::class);
        $detectionStrategy->detect()->shouldBeCalled()->willReturn('en');

        $detector = new Detector($detectionStrategy->reveal());
        $this->assertEquals('en', $detector->lang());
    }

}
