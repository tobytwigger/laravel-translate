<?php

namespace Twigger\Tests\Translate\Unit;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

class TranslateTest extends TestCase
{

    /** @test */
    public function it_calls_the_underlying_class(){
        $manager = $this->prophesize(TranslationManager::class);
        $manager->driver('test')->shouldBeCalled()->willReturn($this->prophesize(Translator::class)->reveal());

        Translate::swap($manager->reveal());

        $this->assertInstanceOf(Translator::class, Translate::driver('test'));

    }

}
