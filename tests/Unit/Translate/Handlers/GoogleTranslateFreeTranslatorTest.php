<?php

namespace Twigger\Tests\Translate\Unit\Translate\Handlers;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Prophecy\Argument;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Handlers\GoogleTranslateFreeTranslator;

class GoogleTranslateFreeTranslatorTest extends TestCase
{

    /** @test */
    public function it_translates_correctly(){
        $googleTranslate = $this->prophesize(GoogleTranslate::class);
        $googleTranslate->setSource('en')->shouldBeCalled();
        $googleTranslate->setTarget('fr')->shouldBeCalled();
        $googleTranslate->translate('Welcome')->shouldBeCalled()->willReturn('Bienvenue');

        $translator = new GoogleTranslateFreeTranslator([], $googleTranslate->reveal());
        $this->assertEquals('Bienvenue', $translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_null_and_logs_if_an_error_is_thrown(){
        $googleTranslate = $this->prophesize(GoogleTranslate::class);
        $googleTranslate->setSource(Argument::any());
        $googleTranslate->setTarget(Argument::any());
        $googleTranslate->translate('Welcome')->shouldBeCalled()->willThrow(new \Exception('Test Message'));

        $log = $this->prophesize(Logger::class);
        $log->warning('Test Message')->shouldBeCalled();
        Log::swap($log->reveal());

        $translator = new GoogleTranslateFreeTranslator([], $googleTranslate->reveal());
        $this->assertNull($translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_null_and_logs_if_an_error_is_thrown_and_log_errors_is_true(){
        $googleTranslate = $this->prophesize(GoogleTranslate::class);
        $googleTranslate->setSource(Argument::any());
        $googleTranslate->setTarget(Argument::any());
        $googleTranslate->translate('Welcome')->shouldBeCalled()->willThrow(new \Exception('Test Message'));

        $log = $this->prophesize(Logger::class);
        $log->warning('Test Message')->shouldBeCalled();
        Log::swap($log->reveal());

        $translator = new GoogleTranslateFreeTranslator([
            'log_errors' => true
        ], $googleTranslate->reveal());
        $this->assertNull($translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_null_and_does_not_log_if_an_error_is_thrown_and_log_errors_is_false(){
        $googleTranslate = $this->prophesize(GoogleTranslate::class);
        $googleTranslate->setSource(Argument::any());
        $googleTranslate->setTarget(Argument::any());
        $googleTranslate->translate('Welcome')->shouldBeCalled()->willThrow(new \Exception('Test Message'));

        $log = $this->prophesize(Logger::class);
        $log->warning('Test Message')->shouldNotBeCalled();
        Log::swap($log->reveal());

        $translator = new GoogleTranslateFreeTranslator([
            'log_errors' => false
        ], $googleTranslate->reveal());
        $this->assertNull($translator->translate('Welcome', 'fr', 'en'));
    }

}
