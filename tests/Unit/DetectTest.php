<?php

namespace Twigger\Tests\Translate\Unit;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Detect;
use Twigger\Translate\Locale\Detector;
use Twigger\Translate\Locale\DetectorFactory;

class DetectTest extends TestCase
{

    /** @test */
    public function it_calls_the_underlying_class(){
        $factory = $this->prophesize(DetectorFactory::class);
        $factory->create()->shouldBeCalled()->willReturn($this->prophesize(Detector::class)->reveal());

        Detect::swap($factory->reveal());

        $this->assertInstanceOf(Detector::class, Detect::create());

    }

}
