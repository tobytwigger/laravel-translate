<?php

namespace Twigger\Tests\Translate\Integration;

use Twigger\Tests\Translate\LaravelTestCase;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\TranslationFactory;
use Twigger\Translate\Translate\Translator;

class TranslateTest extends LaravelTestCase
{

    /** @test */
    public function it_can_translate_text(){
        Translate::pushDriver('test', function($app, $config) {
            return new DummyTranslator($config);
        });

        Translate::pushConfiguration('test-config', 'test', [
            'translations' => [
                'Welcome_fr_en' => 'Bienvenue'
            ]
        ]);

        $this->assertEquals('Bienvenue', Translate::driver('test-config')->translate('Welcome', 'fr', 'en'));
    }

    /** @test */
    public function it_caches_the_translation(){
        Translate::pushDriver('test', function($app, $config) {
            return new DummyTranslator($config);
        });

        Translate::pushConfiguration('test-config', 'test', [
            'translations' => [
                'Welcome_fr_en' => 'Bienvenue'
            ]
        ]);

        app(TranslationFactory::class)->intercept(CacheInterceptor::class);

        DummyTranslator::$called = 0;
        $this->assertEquals('Bienvenue', Translate::driver('test-config')->translate('Welcome', 'fr', 'en'));
        $this->assertEquals(1, DummyTranslator::$called);
        $this->assertEquals('Bienvenue', Translate::driver('test-config')->translate('Welcome', 'fr', 'en'));
        $this->assertEquals(1, DummyTranslator::$called);
    }

}

class DummyTranslator extends Translator
{

    public static $called = 0;

    public function translate(string $line, string $to, string $from): ?string
    {
        static::$called++;
        $key = sprintf('translations.%s_%s_%s', $line, $to, $from);
        return $this->getConfig($key, null);
    }
}
