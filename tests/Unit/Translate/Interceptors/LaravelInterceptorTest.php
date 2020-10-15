<?php

namespace Twigger\Tests\Translate\Unit\Translate\Interceptors;

use Illuminate\Translation\Translator;
use Twigger\Tests\Translate\TestCase;

class LaravelInterceptorTest extends TestCase
{

    /** @test */
    public function has_returns_true_if_the_laravel_translation_has_the_given_key(){
        $this->markTestIncomplete();
        $laravelTranslate = $this->prophesize(Translator::class);
        $laravelTranslate->has('Test Line', [], 'fr')->shouldBeCalled()->willReturn(true);
        $laravelTranslate->has('Test Line', [], 'fr')->shouldBeCalled()->willReturn(true);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);

        $interceptor = new \Twigger\Translate\Translate\Interceptors\LaravelInterceptor(
            [], $translator->reveal(), $laravelTranslate->reveal()
        );
        $interceptor->translate('Test Line', 'fr', 'en');
    }

}
