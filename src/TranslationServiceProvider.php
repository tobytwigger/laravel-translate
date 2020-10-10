<?php

namespace Twigger\Translate;

use Twigger\Translate\Http\Controllers\TranslationController;
use Twigger\Translate\Locale\Strategies\BodyDetectionStrategy;
use Twigger\Translate\Locale\Strategies\CookieDetectionStrategy;
use Twigger\Translate\Locale\DetectionStrategyStore;
use Twigger\Translate\Locale\Strategies\HeaderDetectionStrategy;
use Twigger\Translate\Locale\Strategies\LaravelDetectionStrategy;
use Twigger\Translate\Translate\Handlers\NullTranslator;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\Interceptors\DatabaseInterceptor;
use Twigger\Translate\Translate\TranslationFactory;
use Twigger\Translate\Translate\TranslationManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(DetectionStrategyStore::class);
        $this->app->singleton(TranslationFactory::class);
        $this->app->singleton('portal-translation', function($app) {
            return new TranslationManager($app, $app->make(TranslationFactory::class));
        });
    }

    public function boot()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-translate.php', 'laravel-translate'
        );

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app->when(BodyDetectionStrategy::class)
            ->needs('$requestKey')
            ->give(function($app) {
                return $app['config']['laravel-translate.detection_body_key'];
            });
        $this->app->when(CookieDetectionStrategy::class)
            ->needs('$cookieKey')
            ->give(function($app) {
                return $app['config']['laravel-translate.detection_cookie_key'];
            });
        $this->app->when(HeaderDetectionStrategy::class)
            ->needs('$allowedLanguages')
            ->give(function($app) {
                return $app['config']['laravel-translate.detection_header_allowed_languages'];
            });

        ($this->app->make(DetectionStrategyStore::class))->registerFirst(BodyDetectionStrategy::class);
        ($this->app->make(DetectionStrategyStore::class))->register(CookieDetectionStrategy::class);
        ($this->app->make(DetectionStrategyStore::class))->registerLast(HeaderDetectionStrategy::class);
        ($this->app->make(DetectionStrategyStore::class))->registerLast(LaravelDetectionStrategy::class);

        Route::post(config('laravel-translate.translate_api_url'), [TranslationController::class, 'translate'])->name('translator.translate');

        app(TranslationFactory::class)->intercept(CacheInterceptor::class);
        app(TranslationFactory::class)->intercept(DatabaseInterceptor::class);

        foreach($this->app['config']['laravel-translate.configurations'] as $name => $config) {
            Translate::pushConfiguration($name, $config[TranslationManager::DRIVER_KEY], $config);
        }

        Translate::setDefaultDriver($this->app['config']['laravel-translate.default']);

        Translate::pushDriver('null', function($app, $config) {
            return new NullTranslator($config);
        });
    }

}
