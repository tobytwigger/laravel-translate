<?php

namespace Twigger\Tests\Translate\Unit\Translate;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\TranslationInterceptor;
use Twigger\Translate\Translate\Translator;

class TranslationInterceptorTest extends TestCase
{

    /** @test */
    public function translate_returns_the_intercept_if_interception_possible(){
        $translator = $this->prophesize(Translator::class);
        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $interceptor->save('Welcome', 'fr', 'en', 'Bienvenue');

        $this->assertEquals('Bienvenue', $interceptor->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function translate_retrieves_the_underlying_translation_and_saves_it_if_interception_not_possible(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn('Bienvenue');

        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));

        $this->assertEquals('Bienvenue', $interceptor->translate('Welcome', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome', 'fr', 'en'));

    }

    /** @test */
    public function translate_does_not_save_a_null_value(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn(null);

        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));

        $this->assertNull($interceptor->translate('Welcome', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function translateMany_returns_intercepts_if_all_lines_can_be_intercepted(){
        $translator = $this->prophesize(Translator::class);
        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $interceptor->save('Welcome', 'fr', 'en', 'Bienvenue');
        $interceptor->save('Welcome2', 'fr', 'en', 'Bienvenue2');
        $interceptor->save('Welcome3', 'fr', 'en', 'Bienvenue3');

        $this->assertEquals([
            'Bienvenue', 'Bienvenue2', 'Bienvenue3'
        ], $interceptor->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en'));
    }

    /** @test */
    public function translateMany_translates_and_saves_any_non_interceptable_lines(){
        $translator = $this->prophesize(Translator::class);
        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $translator->translateMany(['Welcome2', 'Welcome3'], 'fr', 'en')->shouldBeCalled()->willReturn(['Bienvenue2', 'Bienvenue3']);

        $interceptor->save('Welcome', 'fr', 'en', 'Bienvenue');

        $this->assertTrue($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome3', 'fr', 'en'));

        $this->assertEquals([
            'Bienvenue', 'Bienvenue2', 'Bienvenue3'
        ], $interceptor->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en'));

        $this->assertTrue($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome3', 'fr', 'en'));
    }

    /** @test */
    public function translateMany_translates_and_saves_all_non_interceptable_lines_if_no_interception_possible(){
        $translator = $this->prophesize(Translator::class);
        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $translator->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en')->shouldBeCalled()->willReturn(['Bienvenue', 'Bienvenue2', 'Bienvenue3']);

        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome3', 'fr', 'en'));

        $this->assertEquals([
            'Bienvenue', 'Bienvenue2', 'Bienvenue3'
        ], $interceptor->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en'));

        $this->assertTrue($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome3', 'fr', 'en'));
    }

    /** @test */
    public function it_does_not_save_many_null_translations_if_ignore_null_is_true(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en')->shouldBeCalled()->willReturn([null, null, null]);

        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $interceptor->ignoreNull = true;


        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome3', 'fr', 'en'));

        $this->assertEquals([
            null, null, null
        ], $interceptor->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en'));

        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome3', 'fr', 'en'));
    }

    /** @test */
    public function it_saves_many_null_translations_if_ignore_null_is_false(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en')->shouldBeCalled()->willReturn([null, null, null]);

        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $interceptor->ignoreNull = false;


        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertFalse($interceptor->canIntercept('Welcome3', 'fr', 'en'));

        $this->assertEquals([
            null, null, null
        ], $interceptor->translateMany(['Welcome', 'Welcome2', 'Welcome3'], 'fr', 'en'));

        $this->assertTrue($interceptor->canIntercept('Welcome', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome2', 'fr', 'en'));
        $this->assertTrue($interceptor->canIntercept('Welcome3', 'fr', 'en'));
    }

    /** @test */
    public function it_does_not_save_a_null_translation_if_ignore_null_is_true(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn(null);

        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $interceptor->ignoreNull = true;


        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));

        $this->assertEquals(null, $interceptor->translate('Welcome', 'fr', 'en'));

        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_saves_a_null_translation_if_ignore_null_is_false(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn(null);

        $interceptor = new TranslationInterceptorTestDummyInterceptor([], $translator->reveal());
        $interceptor->ignoreNull = false;


        $this->assertFalse($interceptor->canIntercept('Welcome', 'fr', 'en'));

        $this->assertEquals(null, $interceptor->translate('Welcome', 'fr', 'en'));

        $this->assertTrue($interceptor->canIntercept('Welcome', 'fr', 'en'));
    }

}

class TranslationInterceptorTestDummyInterceptor extends TranslationInterceptor
{

    public $ignoreNull = true;

    public $intercepts = [];

    public function canIntercept(string $line, string $to, string $from): bool
    {
        return array_key_exists($this->getKey($line, $to, $from), $this->intercepts);
    }

    public function get(string $line, string $to, string $from): string
    {
        return $this->intercepts[$this->getKey($line, $to, $from)];
    }

    public function save(string $line, string $to, string $from, ?string $translation): void
    {
        $this->intercepts[$this->getKey($line, $to, $from)] = $translation;
    }

    private function getKey(string $line, string $to, string $from)
    {
        return md5($line . $to . $from);
    }
}
