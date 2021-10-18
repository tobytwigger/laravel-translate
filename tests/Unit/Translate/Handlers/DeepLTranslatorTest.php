<?php

namespace Twigger\Tests\Translate\Unit\Translate\Handlers;

use BabyMarkt\DeepL\DeepL;
use BabyMarkt\DeepL\DeepLException;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Prophecy\Argument;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Handlers\DeepLTranslator;

class DeepLTranslatorTest extends TestCase
{

    /** @test */
    public function it_translates_correctly(){
        $deepl = $this->prophesize(DeepL::class);
        $deepl->translate('Welcome', 'en', 'fr')->shouldBeCalled()->willReturn([['text' => 'Bienvenue']]);

        $translator = new DeepLTranslator([], $deepl->reveal());
        $this->assertEquals('Bienvenue', $translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_null_and_logs_if_an_error_is_thrown(){
        $deepl = $this->prophesize(DeepL::class);
        $deepl->translate('Welcome', Argument::any(), Argument::any())->shouldBeCalled()->willThrow(new DeepLException('Test Message'));

        $log = $this->prophesize(Logger::class);
        $log->warning('Test Message')->shouldBeCalled();
        Log::swap($log->reveal());

        $translator = new DeepLTranslator([], $deepl->reveal());
        $this->assertNull($translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_null_and_logs_if_an_error_is_thrown_and_log_errors_is_true(){
        $deepl = $this->prophesize(DeepL::class);
        $deepl->translate('Welcome', Argument::any(), Argument::any())->shouldBeCalled()->willThrow(new DeepLException('Test Message'));

        $log = $this->prophesize(Logger::class);
        $log->warning('Test Message')->shouldBeCalled();
        Log::swap($log->reveal());

        $translator = new DeepLTranslator([
            'log_errors' => true
        ], $deepl->reveal());
        $this->assertNull($translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_null_and_does_not_log_if_an_error_is_thrown_and_log_errors_is_false(){
        $deepl = $this->prophesize(DeepL::class);
        $deepl->translate('Welcome', Argument::any(), Argument::any())->shouldBeCalled()->willThrow(new DeepLException('Test Message'));

        $log = $this->prophesize(Logger::class);
        $log->warning('Test Message')->shouldNotBeCalled();
        Log::swap($log->reveal());

        $translator = new DeepLTranslator([
            'log_errors' => false
        ], $deepl->reveal());
        $this->assertNull($translator->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_an_array_from_translateMany(){
        $deepl = $this->prophesize(DeepL::class);
        $deepl->translate(['Welcome', 'thank you'], 'en', 'fr')->shouldBeCalled()->willReturn([['text' => 'bienvenue'], ['text' => 'merci']]);

        $translator = new DeepLTranslator([], $deepl->reveal());
        $this->assertEquals(['bienvenue', 'merci'], $translator->translateMany(['Welcome', 'thank you'], 'fr', 'en'));
    }

}
