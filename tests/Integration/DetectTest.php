<?php

namespace Twigger\Tests\Translate\Integration;

use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Translate\Detect;
use Twigger\Translate\Locale\DetectionStrategy;
use Twigger\Translate\Locale\DetectionStrategyStore;

class DetectTest extends LaravelTestCase
{

    /** @test */
    public function it_can_detect_the_wanted_language(){
        app(DetectionStrategyStore::class)->registerFirst(Detector::class);

        $this->assertEquals('test_UK', Detect::lang());
    }

}

class Detector extends DetectionStrategy
{

    protected function getCurrentLocale(): ?string
    {
        return 'test_UK';
    }
}
