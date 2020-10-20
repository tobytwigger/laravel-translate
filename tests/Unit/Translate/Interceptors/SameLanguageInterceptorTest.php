<?php

namespace Twigger\Tests\Translate\Unit\Translate\Interceptors;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Translation\Translator;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;

class SameLanguageInterceptorTest extends TestCase
{

    /** @test */
    public function it_calls_the_translator_if_the_target_and_source_languages_are_different_for_one_line(){
        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate('Test Line', 'fr', 'en')->shouldBeCalled()->willReturn('Some Test Line');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SameLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals('Some Test Line', $interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_returns_the_single_line_if_the_target_and_source_languages_are_the_same(){
        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SameLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals('Test Line', $interceptor->translate('Test Line', 'en', 'en'));
    }

    /** @test */
    public function it_calls_the_translator_if_the_target_and_source_languages_are_different_for_multiple_line(){
        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translateMany([ 'Test Line', 'Test Line 2'], 'ru', 'en')->shouldBeCalled()->willReturn([
            'Some Test Line',
            'Some Test Line 2'
        ]);

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SameLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals([
            'Some Test Line',
            'Some Test Line 2'
        ], $interceptor->translateMany([
            'Test Line', 'Test Line 2'
        ], 'ru', 'en'));
    }

    /** @test */
    public function it_returns_the_array_of_lines_if_the_target_and_source_languages_are_the_same(){
        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translateMany(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SameLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals([
            'Test Line',
            'Test Line 2'
        ], $interceptor->translateMany([
            'Test Line', 'Test Line 2'
        ], 'ru', 'ru'));
    }

}
