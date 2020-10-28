<?php

namespace Twigger\Tests\Translate\Integration\Http\Controllers\Api;

use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;

class DatabaseTranslationControllerTest extends LaravelTestCase
{

    /** @test */
    public function index_gets_all_translations_in_a_paginator(){
        $translationModels = TranslationModel::factory()->count(5)->create();
        $response = $this->getJson('api/translations');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => $translationModels->toArray()
        ]);
    }

    /** @test */
    public function the_base_url_is_set_in_the_config(){
        config()->set('laravel-translate.ui.url', 'test-translation-api-url');

        $translationModels = TranslationModel::factory()->count(5)->create();
        $response = $this->getJson('/test-translation-api-url');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => $translationModels->toArray()
        ]);
    }

    /** @test */
    public function index_filters_by_search_query_in_original_and_translated(){
        TranslationModel::factory()->count(5)->create();
        $translationModelOrig = TranslationModel::factory()->count(5)->create([
            'text_original' => 'In here is a test string with some extra bits'
        ]);
        $translationModelTrans = TranslationModel::factory()->count(5)->create([
            'text_translated' => 'In here also is a test string with some more extra bits, different to the original text'
        ]);
        TranslationModel::factory()->count(5)->create();

        $response = $this->getJson('/translate/api?search="is a test string"');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => [
                $translationModelOrig->toArray(),
                $translationModelTrans->toArray()
            ]
        ]);
    }

    public function index_filters_by_search_query_in_original_and_translated_and_ignores_case(){
        TranslationModel::factory()->count(5)->create();
        $translationModelOrig = TranslationModel::factory()->count(5)->create([
            'text_original' => 'In here is a test string with some extra bits'
        ]);
        $translationModelTrans = TranslationModel::factory()->count(5)->create([
            'text_translated' => 'In here also is a test string with some more extra bits, different to the original text'
        ]);
        TranslationModel::factory()->count(5)->create();

        $response = $this->getJson('/translate/api?search="IS a teSt stRInG"');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => [
                $translationModelOrig->toArray(),
                $translationModelTrans->toArray()
            ]
        ]);
    }

    /** @test */
    public function index_filters_by_source_lang(){
        TranslationModel::factory()->count(5)->create([
            'lang_from' => 'fr'
        ]);
        $translationModels = TranslationModel::factory()->count(5)->create([
            'lang_from' => 'en'
        ]);
        TranslationModel::factory()->count(5)->create([
            'lang_from' => 'de'
        ]);

        $response = $this->getJson('/translate/api?source="en"');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => $translationModels->toArray()
        ]);
    }

    /** @test */
    public function index_filters_by_target_lang(){
        TranslationModel::factory()->count(5)->create([
            'lang_to' => 'fr'
        ]);
        $translationModels = TranslationModel::factory()->count(5)->create([
            'lang_to' => 'en'
        ]);
        TranslationModel::factory()->count(5)->create([
            'lang_to' => 'de'
        ]);

        $response = $this->getJson('/translate/api?target="en"');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => $translationModels->toArray()
        ]);
    }

    /** @test */
    public function index_filters_by_has_translated_lang()
    {
        TranslationModel::factory()->count(5)->create([
            'text_translated' => 'This is a test string'
        ]);
        $translationModels = TranslationModel::factory()->count(5)->create([
            'text_translated' => null
        ]);
        TranslationModel::factory()->count(5)->create([
            'text_translated' => 'Another test string'
        ]);

        $response = $this->getJson('/translate/api?has_translation=true');
        $response->assertJsonFragment([
            'total_pages' => 1,
            'current_page' => 1
        ]);
        $response->assertJsonFragment([
            'data' => $translationModels->toArray()
        ]);
    }

    /** @test */
    public function index_returns_an_empty_array_if_no_results_are_found(){
        $response = $this->getJson('/translate/api');
        $response->assertJsonFragment([
            'total' => 0
        ]);
    }

    /** @test */
    public function index_can_have_the_items_per_page_set(){

    }

    /** @test */
    public function index_can_have_the_page_number_set(){

    }

    /** @test */
    public function store_creates_a_new_translation(){

    }

    /** @test */
    public function store_throws_an_error_if_the_translation_already_exists(){

    }

    /** @test */
    public function update_updates_a_translation(){

    }

    /** @test */
    public function update_returns_a_404_if_the_translation_does_not_exist(){

    }

    /** @test */
    public function destroy_deletes_a_translation(){

    }

    /** @test */
    public function destroy_returns_a_404_if_the_translation_does_not_exist(){

    }

}
