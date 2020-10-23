<?php

namespace Twigger\Tests\Translate\Unit\Translate\Interceptors;

use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor;

class SupportedLanguageInterceptorTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        SupportedLanguageInterceptor::supportAll();
    }

    protected function tearDown(): void
    {
        SupportedLanguageInterceptor::supportAll();
    }

    /** @test */
    public function it_allows_a_language_if_supported_languages_empty(){
        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldBeCalled()
            ->willReturn('Test Line Translated');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_allows_a_target_language_if_in_supported_languages()
    {
        SupportedLanguageInterceptor::support(['en', 'fr', 'ru', 'en_US']);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldBeCalled()
            ->willReturn('Test Line Translated');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_allows_a_target_language_if_in_supported_languages_and_source_lang_not()
    {
        SupportedLanguageInterceptor::support(['en', 'fr', 'ru', 'en_US']);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldBeCalled()
            ->willReturn('Test Line Translated');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );
        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'fr', 'en_GB'));
    }

    /** @test */
    public function it_denies_a_language_if_source_language_not_in_supported_languages()
    {
        SupportedLanguageInterceptor::support(['en', 'ru', 'en_US']);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );

        $this->assertNull($interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_allows_multiple_calls_to_support()
    {
        SupportedLanguageInterceptor::support(['ru']);
        SupportedLanguageInterceptor::support(['fr']);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldBeCalled()
            ->willReturn('Test Line Translated');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );

        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'ru', 'en'));
        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_allows_a_language_to_be_revoked()
    {
        SupportedLanguageInterceptor::support(['ru', 'fr']);
        SupportedLanguageInterceptor::revoke(['fr']);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldBeCalled()
            ->willReturn('Test Line Translated');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );

        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'ru', 'en'));
        $this->assertNull($interceptor->translate('Test Line', 'fr', 'en'));
    }

    /** @test */
    public function it_revokes_all_languages()
    {
        SupportedLanguageInterceptor::support(['ru', 'fr']);
        SupportedLanguageInterceptor::supportAll();
        SupportedLanguageInterceptor::support(['en']);

        $translator = $this->prophesize(\Twigger\Translate\Translate\Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldBeCalled()
            ->willReturn('Test Line Translated');

        $interceptor = new \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor(
            [], $translator->reveal()
        );

        $this->assertNull($interceptor->translate('Test Line', 'ru', 'en'));
        $this->assertNull($interceptor->translate('Test Line', 'fr', 'en'));
        $this->assertEquals('Test Line Translated', $interceptor->translate('Test Line', 'en', 'en'));
    }

}
