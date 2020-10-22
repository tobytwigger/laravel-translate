<?php

namespace Twigger\Tests\Translate\Unit\Locale;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\DetectionStrategy;

class DetectionStrategyTest extends TestCase
{

    /** @test */
    public function detect_returns_the_first_strategy_if_it_returns_a_string(){
        $strategy1 = new Strategy1('en');
        $strategy2 = new Strategy2('fr');

        $strategy1->setNext($strategy2);

        $this->assertEquals('en', $strategy1->detect());
    }

    /** @test */
    public function detect_returns_the_second_strategy_if_the_first_returns_null(){
        $strategy1 = new Strategy1(null);
        $strategy2 = new Strategy2('fr');

        $strategy1->setNext($strategy2);

        $this->assertEquals('fr', $strategy1->detect());
    }

    /** @test */
    public function detect_returns_null_if_both_strategies_return_null(){
        $strategy1 = new Strategy1(null);
        $strategy2 = new Strategy2(null);

        $strategy1->setNext($strategy2);

        $this->assertNull($strategy1->detect());
    }

}

class DummyStrategy extends DetectionStrategy
{

    private $locale;

    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    protected function getCurrentLocale(): ?string
    {
        return $this->locale;
    }
}

class Strategy1 extends DummyStrategy {}
class Strategy2 extends DummyStrategy {}
