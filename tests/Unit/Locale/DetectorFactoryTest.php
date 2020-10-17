<?php

namespace Twigger\Tests\Translate\Unit\Locale;

use Illuminate\Contracts\Foundation\Application;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\DetectionStrategy;
use Twigger\Translate\Locale\DetectionStrategyStore;
use Twigger\Translate\Locale\Detector;
use Twigger\Translate\Locale\DetectorFactory;

class DetectorFactoryTest extends TestCase
{

    /** @test */
    public function create_returns_a_chain_of_detection_strategies_in_a_detector(){

        $detectionStrategyStore = $this->prophesize(DetectionStrategyStore::class);
        $detectionStrategyStore->all()->willReturn([
            DummyStrategy1::class, DummyStrategy2::class
        ]);
        $application = $this->prophesize(Application::class);
        $application->make(DummyStrategy1::class)->willReturn(new DummyStrategy1());
        $application->make(DummyStrategy2::class)->willReturn(new DummyStrategy2());

        $factory = new DetectorFactory($application->reveal(), $detectionStrategyStore->reveal());
        $detector = $factory->create();

        $strategiesProperty = new \ReflectionProperty(Detector::class, 'detectionStrategy');
        $strategiesProperty->setAccessible(true);
        $strategies = $strategiesProperty->getValue($detector);

        $this->assertInstanceOf(DummyStrategy1::class, $strategies);

        $secondStrategiesProperty = new \ReflectionProperty(DetectionStrategy::class, 'successor');
        $secondStrategiesProperty->setAccessible(true);
        $secondStrategy = $secondStrategiesProperty->getValue($strategies);

        $this->assertInstanceOf(DummyStrategy2::class, $secondStrategy);
    }

    /** @test */
    public function create_throws_an_exception_if_no_detection_strategies_registered(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No locale detection strategies registered');

        $detectionStrategyStore = $this->prophesize(DetectionStrategyStore::class);
        $detectionStrategyStore->all()->willReturn([]);
        $application = $this->prophesize(Application::class);

        $factory = new DetectorFactory($application->reveal(), $detectionStrategyStore->reveal());
        $detector = $factory->create();

        $strategiesProperty = new \ReflectionProperty(Detector::class, 'detectionStrategy');
        $strategiesProperty->setAccessible(true);
        $strategies = $strategiesProperty->getValue($detector);

        $this->assertNull($strategies);
    }

    /** @test */
    public function create_works_with_a_single_detection_strategy(){
        $detectionStrategyStore = $this->prophesize(DetectionStrategyStore::class);
        $detectionStrategyStore->all()->willReturn([
            DummyStrategy1::class
        ]);
        $application = $this->prophesize(Application::class);
        $application->make(DummyStrategy1::class)->willReturn(new DummyStrategy1());

        $factory = new DetectorFactory($application->reveal(), $detectionStrategyStore->reveal());
        $detector = $factory->create();

        $strategiesProperty = new \ReflectionProperty(Detector::class, 'detectionStrategy');
        $strategiesProperty->setAccessible(true);
        $strategies = $strategiesProperty->getValue($detector);

        $this->assertInstanceOf(DummyStrategy1::class, $strategies);

        $secondStrategiesProperty = new \ReflectionProperty(DetectionStrategy::class, 'successor');
        $secondStrategiesProperty->setAccessible(true);
        $secondStrategy = $secondStrategiesProperty->getValue($strategies);

        $this->assertNull($secondStrategy);
    }

    /** @test */
    public function the_factory_can_be_used_as_a_proxy_for_the_detector(){
        $detectionStrategyStore = $this->prophesize(DetectionStrategyStore::class);
        $detectionStrategyStore->all()->willReturn([
            DummyStrategy1::class, DummyStrategy2::class
        ]);
        $application = $this->prophesize(Application::class);
        $application->make(DummyStrategy1::class)->willReturn(new DummyStrategy1());
        $application->make(DummyStrategy2::class)->willReturn(new DummyStrategy2());

        $factory = new DetectorFactory($application->reveal(), $detectionStrategyStore->reveal());

        $this->assertEquals('en', $factory->lang());
    }

}

class DummyStrategy1 extends DetectionStrategy {

    protected function getCurrentLocale(): ?string
    {
        return 'en';
    }
}

class DummyStrategy2 extends DetectionStrategy {

    protected function getCurrentLocale(): ?string
    {
        return 'fr';
    }
}
