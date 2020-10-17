<?php

namespace Twigger\Tests\Translate\Unit\Translate\Interceptors;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Translation\Translator;
use Twigger\Tests\Translate\TestCase;

class LaravelInterceptorTest extends TestCase
{

    /** @test */
    public function it_calls_the_translator_if_the_laravel_translation_does_not_have_the_given_translation(){
        $app = $this->prophesize(Application::class);
        $app->isLocale('en')->shouldBeCalled()->willReturn(true);
        App::swap($app->reveal());

        $laravelTranslate = $this->prophesize(Translator::class);
        $laravelTranslate->has('Test Line', [], 'fr')->shouldBeCalled()->willReturn(false);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate('Test Line', 'fr', 'en')->shouldBeCalled()->willReturn('Some Test Line');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\LaravelInterceptor(
            [], $translator->reveal(), $laravelTranslate->reveal()
        );
        $this->assertEquals('Some Test Line', $interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_the_value_of_the_translator_if_the_laravel_translation_has_the_given_translation(){
        $app = $this->prophesize(Application::class);
        $app->isLocale('ru')->shouldBeCalled()->willReturn(true);
        App::swap($app->reveal());

        $laravelTranslate = $this->prophesize(Translator::class);
        $laravelTranslate->has('Test Line', [], 'fr')->shouldBeCalled()->willReturn(true);
        $laravelTranslate->get('Test Line', [], 'fr')->shouldBeCalled()->willReturn('Some Other Test Line');

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate('Test Line', 'fr', 'ru')->shouldNotBeCalled();

        $interceptor = new \Twigger\Translate\Translate\Interceptors\LaravelInterceptor(
            [], $translator->reveal(), $laravelTranslate->reveal()
        );
        $this->assertEquals('Some Other Test Line', $interceptor->translate('Test Line', 'fr', 'ru'));;
    }

    /** @test */
    public function it_calls_the_translator_if_the_app_locale_is_different_to_the_requested_locale(){
        $app = $this->prophesize(Application::class);
        $app->isLocale('en')->shouldBeCalled()->willReturn(false);
        App::swap($app->reveal());

        $laravelTranslate = $this->prophesize(Translator::class);
        $laravelTranslate->has('Test Line', [], 'fr')->shouldNotBeCalled();

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate('Test Line', 'fr', 'en')->shouldBeCalled()->willReturn('Some Test Line');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\LaravelInterceptor(
            [], $translator->reveal(), $laravelTranslate->reveal()
        );
        $this->assertEquals('Some Test Line', $interceptor->translate('Test Line', 'fr', 'en'));
    }

}
