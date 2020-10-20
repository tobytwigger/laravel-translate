<?php

namespace Twigger\Tests\Translate\Integration;

use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Translate\Detect;
use Twigger\Translate\Locale\DetectorFactory;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

class helpersTest extends LaravelTestCase
{

    /** @test */
    public function laravelTranslate_returns_an_instance_of_the_translation_manager()
    {
        $translationManager = \laravelTranslate();
        $this->assertInstanceOf(TranslationManager::class, $translationManager);
    }

    /** @test */
    public function laravelTranslate_translates_a_line()
    {
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn('Line 1 translated');
        $this->app->instance('laravel-translate', $translator->reveal());

        $this->assertEquals('Line 1 translated', \laravelTranslate('Line 1', 'fr', 'en'));
    }

    /** @test */
    public function laravelTranslate_uses_the_default_from_and_to_languages()
    {
        config()->set('app.locale', 'de');

        $detector = $this->prophesize(\Twigger\Translate\Locale\Detector::class);
        $detector->lang()->shouldBeCalled()->willReturn('ru');
        Detect::swap($detector->reveal());

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Line 1', 'ru', 'de')->shouldBeCalled()->willReturn('Line 1 translated');
        $this->app->instance('laravel-translate', $translator->reveal());

        $this->assertEquals('Line 1 translated', \laravelTranslate('Line 1'));
    }

    /** @test */
    public function __t_returns_an_instance_of_the_translation_manager()
    {
        $translationManager = \__t();
        $this->assertInstanceOf(TranslationManager::class, $translationManager);
    }

    /** @test */
    public function __t_translates_a_line()
    {
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn('Line 1 translated');
        $this->app->instance('laravel-translate', $translator->reveal());

        $this->assertEquals('Line 1 translated', \__t('Line 1', 'fr', 'en'));
    }

    /** @test */
    public function __t_uses_the_default_from_and_to_languages()
    {
        config()->set('app.locale', 'de');

        $detector = $this->prophesize(\Twigger\Translate\Locale\Detector::class);
        $detector->lang()->shouldBeCalled()->willReturn('ru');
        Detect::swap($detector->reveal());

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Line 1', 'ru', 'de')->shouldBeCalled()->willReturn('Line 1 translated');
        $this->app->instance('laravel-translate', $translator->reveal());

        $this->assertEquals('Line 1 translated', \__t('Line 1'));
    }

}
