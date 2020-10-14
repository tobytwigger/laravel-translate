<?php

namespace Twigger\Tests\Translate\Integration\Http\Controllers;

use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Translate\Translate;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

class TranslationControllerTest extends LaravelTestCase
{

    /** @test */
    public function it_translates_using_a_single_line_given_and_resolves_the_target_and_source_lang_from_the_request(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test Line 1', 'ru', 'fr')->shouldBeCalled()->willReturn('Test Line 1 in ru');

        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        $response = $this->postJson('_translate', [
            'line' => 'Test Line 1',
            'source_lang' => 'fr',
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translation' => 'Test Line 1 in ru'
        ]);
    }

    /** @test */
    public function it_resolves_the_target_language_from_the_request_using_the_detector_if_not_given(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test Line 1', 'ru', 'fr')->shouldBeCalled()->willReturn('Test Line 1 in ru');
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        config()->set('laravel-translate.detection_body_key', 'some_random_key');

        $response = $this->postJson('_translate', [
            'line' => 'Test Line 1',
            'source_lang' => 'fr',
            'some_random_key' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translation' => 'Test Line 1 in ru'
        ]);
    }

    /** @test */
    public function it_resolves_the_default_source_language_from_the_config_if_not_given(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test Line 1', 'ru', 'fr')->shouldBeCalled()->willReturn('Test Line 1 in ru');
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        config()->set('laravel-translate.default_language', 'fr');

        $response = $this->postJson('_translate', [
            'line' => 'Test Line 1',
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translation' => 'Test Line 1 in ru'
        ]);
    }

    /** @test */
    public function it_translates_using_multiple_lines_given_and_resolves_the_target_and_source_lang_from_the_request(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test Line 1', 'Test Line 2'], 'ru', 'fr')
            ->shouldBeCalled()
            ->willReturn(['Test Line 1 in ru', 'Test Line 2 in ru']);
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        $response = $this->postJson('_translate', [
            'lines' => ['Test Line 1', 'Test Line 2'],
            'source_lang' => 'fr',
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translations' => ['Test Line 1 in ru', 'Test Line 2 in ru']
        ]);
    }

    /** @test */
    public function it_resolves_the_target_language_from_the_request_using_the_detector_if_not_given_when_lines_is_an_array(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test Line 1', 'Test Line 2'], 'ru', 'fr')
            ->shouldBeCalled()
            ->willReturn(['Test Line 1 in ru', 'Test Line 2 in ru']);
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        config()->set('laravel-translate.detection_body_key', 'some_random_key');

        $response = $this->postJson('_translate', [
            'lines' => ['Test Line 1', 'Test Line 2'],
            'source_lang' => 'fr',
            'some_random_key' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translations' => ['Test Line 1 in ru', 'Test Line 2 in ru']
        ]);
    }

    /** @test */
    public function it_resolves_the_default_source_language_from_the_config_if_not_given_using_multiple_lines(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test Line 1', 'Test Line 2'], 'ru', 'fr')
            ->shouldBeCalled()
            ->willReturn(['Test Line 1 in ru', 'Test Line 2 in ru']);
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        config()->set('laravel-translate.default_language', 'fr');

        $response = $this->postJson('_translate', [
            'lines' => ['Test Line 1', 'Test Line 2'],
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translations' => ['Test Line 1 in ru', 'Test Line 2 in ru']
        ]);
    }


    /** @test */
    public function it_returns_the_original_single_line_if_translation_is_null(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test Line 1', 'ru', 'fr')->shouldBeCalled()->willReturn(null);

        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        $response = $this->postJson('_translate', [
            'line' => 'Test Line 1',
            'source_lang' => 'fr',
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translation' => 'Test Line 1'
        ]);
    }

    /** @test */
    public function it_returns_the_original_multiple_lines_if_translation_is_null(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test Line 1', 'Test Line 2', 'Test Line 3'], 'ru', 'fr')
            ->shouldBeCalled()
            ->willReturn([null, 'Test Line 2 in ru', null]);
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        $response = $this->postJson('_translate', [
            'lines' => ['Test Line 1', 'Test Line 2', 'Test Line 3'],
            'source_lang' => 'fr',
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'translations' => ['Test Line 1', 'Test Line 2 in ru', 'Test Line 3']
        ]);
    }

    /** @test */
    public function it_returns_the_translations_in_order(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test Line 1', 'Test Line 2', 'Test Line 3'], 'ru', 'fr')
            ->shouldBeCalled()
            ->willReturn([null, 'Test Line 2 in ru', null]);
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        $response = $this->postJson('_translate', [
            'lines' => ['Test Line 1', 'Test Line 2', 'Test Line 3'],
            'source_lang' => 'fr',
            'target_lang' => 'ru'
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'translations' => ['Test Line 1', 'Test Line 2 in ru', 'Test Line 3']
        ]);
    }

}
