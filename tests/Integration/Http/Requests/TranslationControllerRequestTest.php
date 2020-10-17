<?php

namespace Twigger\Tests\Translate\Integration\Http\Requests;

use Prophecy\Argument;
use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Translate\Detect;
use Twigger\Translate\Locale\Detector;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

class TranslationControllerRequestTest extends LaravelTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->will(function($args) {
            return $args[0];
        });
        $translator->translateMany(Argument::any(), Argument::any(), Argument::any())->will(function($args) {
            return $args[0];
        });
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());

        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        config()->set('app.locale', 'ru');
    }

    /** @test */
    public function it_makes_a_valid_request_with_a_single_line(){
        $response = $this->postJson('_translate', [
            'line' => 'Line',
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_makes_a_valid_request_with_multiple_lines(){
        $response = $this->postJson('_translate', [
            'lines' => ['Line1', 'line2'],
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function if_the_target_lang_is_not_given_it_is_detected(){

        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), 'fr', Argument::any())->will(function($args) {
            return $args[0];
        });
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        $detector = $this->prophesize(Detector::class);
        $detector->lang()->shouldBeCalled()->willReturn('fr');

        Detect::swap($detector->reveal());

        $response = $this->postJson('_translate', [
            'line' => 'line1',
            'source_lang' => 'en',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function if_the_source_lang_is_not_given_it_is_taken_from_the_config(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), Argument::any(), 'ru')->will(function($args) {
            return $args[0];
        });
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());

        config()->set('app.locale', 'ru');

        $response = $this->postJson('_translate', [
            'line' => 'Line1',
            'target_lang' => 'en',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_if_line_and_lines_arent_given(){
        $response = $this->postJson('_translate', [
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'line' => 'Either the line or lines key must be given',
            'lines' => 'Either the line or lines key must be given'
        ]);
    }

    /** @test */
    public function it_validates_if_both_line_and_lines_are_given(){
        $response = $this->postJson('_translate', [
            'line' => 'Test Line',
            'lines' => ['test', 'one'],
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'line' => 'Only one of line or lines may be given',
            'lines' => 'Only one of line or lines may be given'
        ]);
    }

    /** @test */
    public function it_validates_if_both_line_and_lines_are_given_but_line_is_empty(){
        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test', 'One'], Argument::any(), Argument::any())->will(function($args) {
            return $args[0];
        });
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());


        $response = $this->postJson('_translate', [
            'line' => '',
            'lines' => ['Test', 'One'],
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'line' => 'Only one of line or lines may be given',
            'lines' => 'Only one of line or lines may be given'
        ]);
    }

    /** @test */
    public function it_validates_if_both_line_and_lines_are_given_but_lines_is_empty(){
        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test', Argument::any(), Argument::any())->will(function($args) {
            return $args[0];
        });
        $translationFactory = $this->prophesize(TranslationManager::class);
        $translationFactory->driver(null)->willReturn($translator->reveal());
        $this->app->instance(TranslationManager::class, $translationFactory->reveal());


        $response = $this->postJson('_translate', [
            'line' => 'Test',
            'lines' => [],
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'line' => 'Only one of line or lines may be given',
            'lines' => 'Only one of line or lines may be given'
        ]);
    }

    /** @test */
    public function it_validates_any_lines_that_are_not_strings(){
        $response = $this->postJson('_translate', [
            'lines' => ['Line1', 444, new class{}],
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'lines.1' => 'The line to translate must be a string',
            'lines.2' => 'The line to translate must be a string'
        ]);
    }

    /** @test */
    public function it_validates_if_lines_is_a_string(){
        $response = $this->postJson('_translate', [
            'lines' => 'Test Line',
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'lines' => 'An array must be given',
        ]);
    }

    /** @test */
    public function it_validates_if_line_is_an_array(){
        $response = $this->postJson('_translate', [
            'line' => ['test'],
            'source_lang' => 'en',
            'target_lang' => 'fr'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'line' => 'A string must be given',
        ]);
    }

    /** @test */
    public function it_validates_if_target_lang_is_an_integer(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'source_lang' => 'en',
            'target_lang' => 55
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'target_lang' => 'The target language must be an ISO-639-1 language code',
        ]);
    }

    /** @test */
    public function it_validates_if_target_lang_is_long(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'source_lang' => 'en',
            'target_lang' => 'not_a_code_as_too_long'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'target_lang' => 'The target language must be an ISO-639-1 language code',
        ]);
    }

    /** @test */
    public function it_validates_if_target_lang_is_empty(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'source_lang' => 'en',
            'target_lang' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'target_lang' => 'The target language must be an ISO-639-1 language code',
        ]);
    }


    /** @test */
    public function it_validates_if_target_lang_is_wrong_code(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'source_lang' => 'en',
            'target_lang' => 'zzni'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'target_lang' => 'The target language must be an ISO-639-1 language code',
        ]);
    }

    /** @test */
    public function it_validates_if_source_lang_is_an_integer(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'target_lang' => 'en',
            'source_lang' => 55
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'source_lang' => 'The source language must be an ISO-639-1 language code',
        ]);
    }

    /** @test */
    public function it_validates_if_source_lang_is_long(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'target_lang' => 'en',
            'source_lang' => 'not_a_code_as_too_long'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'source_lang' => 'The source language must be an ISO-639-1 language code',
        ]);
    }

    /** @test */
    public function it_validates_if_source_lang_is_empty(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'target_lang' => 'en',
            'source_lang' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'source_lang' => 'The source language must be an ISO-639-1 language code',
        ]);
    }


    /** @test */
    public function it_validates_if_source_lang_is_wrong_code(){
        $response = $this->postJson('_translate', [
            'line' => 'test',
            'target_lang' => 'en',
            'source_lang' => 'zzni'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'source_lang' => 'The source language must be an ISO-639-1 language code',
        ]);
    }

}
