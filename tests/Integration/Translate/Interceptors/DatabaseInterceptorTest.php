<?php

namespace Twigger\Tests\Translate\Integration\Translate\Interceptors;

use Prophecy\Argument;
use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;
use Twigger\Translate\Translate\Interceptors\DatabaseInterceptor;
use Twigger\Translate\Translate\Translator;

class DatabaseInterceptorTest extends LaravelTestCase
{

    /** @test */
    public function it_returns_a_single_translation_from_the_db_if_possible()
    {
        TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals('Bienvenue', $interceptor->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_does_not_return_a_single_translation_from_the_db_if_translation_is_null_and_it_updates_the_value()
    {
        TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => null,
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn('Bienvenue');

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals('Bienvenue', $interceptor->translate('Welcome', 'fr', 'en'));

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
    }

    /** @test */
    public function it_saves_a_new_translation_in_the_db()
    {
        $this->assertDatabaseMissing('translations', [
            'text_original' => 'Welcome',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn('Bienvenue');

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals('Bienvenue', $interceptor->translate('Welcome', 'fr', 'en'));

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
    }

    /** @test */
    public function it_saves_a_new_translation_in_the_db_even_if_it_is_null()
    {
        $this->assertDatabaseMissing('translations', [
            'text_original' => 'Welcome',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Welcome', 'fr', 'en')->shouldBeCalled()->willReturn(null);

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertNull($interceptor->translate('Welcome', 'fr', 'en'));

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome',
            'text_translated' => null,
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
    }

    /** @test */
    public function it_can_return_and_save_a_mixture_of_translations_and_db_translations()
    {
        TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
        TranslationModel::create([
            'text_original' => 'Welcome2',
            'text_translated' => 'Bienvenue2',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Welcome3', 'Welcome4'], 'fr', 'en')->shouldBeCalled()->willReturn(['Bienvenue3', 'Bienvenue4']);

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals([
            'Bienvenue', 'Bienvenue2', 'Bienvenue3', 'Bienvenue4'], $interceptor->translateMany([
                'Welcome', 'Welcome2', 'Welcome3', 'Welcome4'
        ], 'fr', 'en'));

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome3',
            'text_translated' => 'Bienvenue3',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome4',
            'text_translated' => 'Bienvenue4',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

    }

    /** @test */
    public function it_can_return_and_save_a_mixture_of_translations_and_db_translations_if_some_are_null()
    {
        TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => null,
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
        TranslationModel::create([
            'text_original' => 'Welcome2',
            'text_translated' => 'Bienvenue2',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Welcome', 'Welcome3', 'Welcome4'], 'fr', 'en')->shouldBeCalled()->willReturn([null, null, 'Bienvenue4']);

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals([
            null, 'Bienvenue2', null, 'Bienvenue4'], $interceptor->translateMany([
            'Welcome', 'Welcome2', 'Welcome3', 'Welcome4'
        ], 'fr', 'en'));

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome3',
            'text_translated' => null,
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome4',
            'text_translated' => 'Bienvenue4',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
    }

    /** @test */
    public function it_can_return_all_translations_from_the_db()
    {
        TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
        TranslationModel::create([
            'text_original' => 'Welcome2',
            'text_translated' => 'Bienvenue2',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals([
            'Bienvenue', 'Bienvenue2'], $interceptor->translateMany([
            'Welcome', 'Welcome2'
        ], 'fr', 'en'));

    }

    /** @test */
    public function it_can_return_and_save_all_translations_from_a_translator()
    {
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Welcome', 'Welcome2'], 'fr', 'en')->shouldBeCalled()->willReturn(['Bienvenue', 'Bienvenue2']);

        $interceptor = new DatabaseInterceptor([], $translator->reveal());
        $this->assertEquals([
            'Bienvenue', 'Bienvenue2'], $interceptor->translateMany([
            'Welcome', 'Welcome2'
        ], 'fr', 'en'));

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome2',
            'text_translated' => 'Bienvenue2',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
    }

}
