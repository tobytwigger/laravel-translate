<?php

namespace Twigger\Tests\Translate\Integration\Translate\Interceptors\Database;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;
use Twigger\Translate\Translate\Interceptors\DatabaseInterceptor;

class TranslationModelTest extends LaravelTestCase
{

    /** @test */
    public function a_model_can_be_created(){
        $this->assertDatabaseMissing('translations', [
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $this->assertDatabaseHas('translations', [
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);
    }

    /** @test */
    public function it_creates_a_reproducable_id_on_creation_based_on_the_languages_and_line(){
        $key = 'en_fr_' .  md5(DatabaseInterceptor::class . 'Welcome');

        $translation = TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $this->assertEquals($key, $translation->id);
        $this->assertDatabaseHas('translations', ['id' => $key]);

        $translation->delete();

        $this->assertDatabaseMissing('translations', ['id' => $key]);
        $translation = TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $this->assertEquals($key, $translation->id);
        $this->assertDatabaseHas('translations', ['id' => $key]);
    }

    /** @test */
    public function it_can_retrieve_the_translation_of_a_given_line_from_a_given_language_to_a_given_language(){
        $translation = TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $resolvedTranslation = TranslationModel::from('en')->to('fr')->translate('Welcome')->firstOrFail();

        $this->assertInstanceOf(TranslationModel::class, $resolvedTranslation);
        $this->assertTrue($resolvedTranslation->exists);
        $this->assertEquals('Bienvenue', $resolvedTranslation->text_translated);
    }

    /** @test */
    public function when_a_translation_is_created_the_database_cache_is_cleared(){
        Cache::forever(CacheInterceptor::getCacheKey('Welcome', 'fr', 'en'), 'Test 2');

        $this->assertTrue(Cache::has(CacheInterceptor::getCacheKey('Welcome', 'fr', 'en')));

        $translation = TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        $this->assertFalse(Cache::has(CacheInterceptor::getCacheKey('Welcome', 'fr', 'en')));

    }

    /** @test */
    public function when_a_translation_is_updated_the_database_cache_is_cleared(){
        $translation = TranslationModel::create([
            'text_original' => 'Welcome',
            'text_translated' => 'Bienvenue',
            'lang_from' => 'en',
            'lang_to' => 'fr'
        ]);

        Cache::forever(CacheInterceptor::getCacheKey('Welcome', 'fr', 'en'), 'Test 2');

        $this->assertTrue(Cache::has(CacheInterceptor::getCacheKey('Welcome', 'fr', 'en')));

        $translation->text_translated = 'Bienvenue2';
        $translation->save();

        $this->assertFalse(Cache::has(CacheInterceptor::getCacheKey('Welcome', 'fr', 'en')));

    }

}
