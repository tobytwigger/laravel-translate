<?php

namespace Twigger\Translate;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Twigger\Translate\Http\Controllers\TranslationController;
use Twigger\Translate\Locale\Strategies\BodyDetectionStrategy;
use Twigger\Translate\Locale\Strategies\CookieDetectionStrategy;
use Twigger\Translate\Locale\DetectionStrategyStore;
use Twigger\Translate\Locale\Strategies\HeaderDetectionStrategy;
use Twigger\Translate\Locale\Strategies\FallbackDetectionStrategy;
use Twigger\Translate\Translate\Handlers\AWSTranslator;
use Twigger\Translate\Translate\Handlers\DeepLTranslator;
use Twigger\Translate\Translate\Handlers\GoogleTranslateFreeTranslator;
use Twigger\Translate\Translate\Handlers\NullTranslator;
use Twigger\Translate\Translate\Handlers\StackTranslator;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\Interceptors\DatabaseInterceptor;
use Twigger\Translate\Translate\Interceptors\LangFileInterceptor;
use Twigger\Translate\Translate\Interceptors\SameLanguageInterceptor;
use Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor;
use Twigger\Translate\Translate\TranslationFactory;
use Twigger\Translate\Translate\TranslationManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Twigger\Translate\Translate\Translator;

/**
 * The service provider for loading Laravel Translate
 */
class TranslationServiceProvider extends ServiceProvider
{

    /**
     * Should the body language detector be loaded?
     *
     * @var bool
     */
    protected static $withBodyDetector = true;

    /**
     * Should the cookie language detector be loaded?
     *
     * @var bool
     */
    protected static $withCookieDetector = true;

    /**
     * Should the header language detector be loaded?
     *
     * @var bool
     */
    protected static $withHeaderDetector = true;
    /**
     * @var bool
     */

    /**
     * If false, the cache won't be used
     *
     * @var bool Should the cache interceptor be used
     */
    protected static $withCacheInterceptor = true;

    /**
     * If false, the database override won't be used
     *
     * @var bool Should the database interceptor be used
     */
    protected static $withDatabaseInterceptor = true;

    /**
     * If false, the language files won't be looked at
     *
     * @var bool Should the language files be used
     */
    protected static $withLangFileInterceptor = true;

    /**
     * Don't use the body detector.
     *
     * The body detector detects the target language from the request body
     *
     * @param bool $value False to not register the detector
     */
    public static function withoutBodyDetector(bool $value = false)
    {
        static::$withBodyDetector = $value;
    }

    /**
     * Don't use the cookie detector.
     *
     * The cookie detector uses a cookie to find the target language
     *
     * @param bool $value False to not register the detector
     */
    public static function withoutCookieDetector(bool $value = false)
    {
        static::$withBodyDetector = $value;
    }

    /**
     * Don't use the header detector.
     *
     * The header detector detects the target language from the request. Most modern browsers automatically add
     * the necessary headers to any request based on user language preferences.
     *
     * @param bool $value False to not register the detector
     */
    public static function withoutHeaderDetector(bool $value = false)
    {
        static::$withBodyDetector = $value;
    }

    /**
     * Don't use caching for translations
     *
     * @param bool $value
     */
    public static function withoutCache(bool $value = false)
    {
        static::$withCacheInterceptor = $value;
    }

    /**
     * Don't override translations using the database
     *
     * @param bool $value
     */
    public static function withoutDatabaseOverrides(bool $value = false)
    {
        static::$withDatabaseInterceptor = $value;
    }

    /**
     * Don't load the lang files
     *
     * @param bool $value
     */
    public static function withoutLangFiles(bool $value = false)
    {
        static::$withLangFileInterceptor = $value;
    }

    /**
     * Bind service classes into the container
     */
    public function register()
    {
        $this->app->singleton(DetectionStrategyStore::class);
        $this->app->singleton(TranslationFactory::class);
        $this->app->singleton('laravel-translate', function ($app) {
            return new TranslationManager($app, $app->make(TranslationFactory::class));
        });
    }

    /**
     * Boot the translation services
     *
     * - Allow assets to be published
     * - Register any detectors to use
     * - Register any interceptors to use
     * - Load drivers
     * - Load configuration
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->publishAssets();
        $this->setupDetector();
        $this->setupInterceptors();
        $this->loadDrivers();
        $this->parseConfig();
        $this->defineBladeDirectives();

        Route::middleware('cache.headers:public;max_age=604800;etag')
            ->get(config('laravel-translate.translate_api_url'), [TranslationController::class, 'translate'])
            ->name('translator.translate');

    }

    /**
     * Publish any assets to allow the end user to customise the functionality of this package
     */
    private function publishAssets()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-translate.php', 'laravel-translate'
        );

        $this->publishes([
            __DIR__ . '/../config/laravel-translate.php' => config_path('laravel-translate.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register any detection strategies.
     *
     * The following detection strategies are registered by default. Most can be turned off by calling the relevant
     * static function.
     * - Body detector. Detect language using a body key.
     * - Cookie detector. Detect language using the value of a cookie.
     * - Header detector. Detect the language based on the headers, given by user browser preferences
     * - Fallback detector. Use the config fallback locale
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function setupDetector()
    {
        if (static::$withBodyDetector) {
            $this->app->make(DetectionStrategyStore::class)->registerFirst(BodyDetectionStrategy::class);
            $this->app->when(BodyDetectionStrategy::class)
                ->needs('$requestKey')
                ->give(function ($app) {
                    return $app['config']['laravel-translate.detection.body_key'];
                });
        }

        if (static::$withCookieDetector) {
            $this->app->make(DetectionStrategyStore::class)->register(CookieDetectionStrategy::class);
            $this->app->when(CookieDetectionStrategy::class)
                ->needs('$cookieKey')
                ->give(function ($app) {
                    return $app['config']['laravel-translate.detection.cookie_key'];
                });
        }

        if (static::$withHeaderDetector) {
            $this->app->make(DetectionStrategyStore::class)->registerLast(HeaderDetectionStrategy::class);
            $this->app->when(HeaderDetectionStrategy::class)
                ->needs('$allowedLanguages')
                ->give(function ($app) {
                    return $app['config']['laravel-translate.detection.header.allowed_languages'];
                });
        }

        $this->app->make(DetectionStrategyStore::class)->registerLast(FallbackDetectionStrategy::class);
    }

    /**
     * Register any given interceptors
     */
    private function setupInterceptors()
    {
        $translationFactory = app(TranslationFactory::class);

        $translationFactory->intercept(SameLanguageInterceptor::class);

        $translationFactory->intercept(SupportedLanguageInterceptor::class);
        SupportedLanguageInterceptor::support(config('laravel-translate.supported_languages', []));

        if(static::$withCacheInterceptor) {
            $translationFactory->intercept(CacheInterceptor::class);
        }

        if(static::$withDatabaseInterceptor) {
            $translationFactory->intercept(DatabaseInterceptor::class);
        }

        if(static::$withLangFileInterceptor) {
            $translationFactory->intercept(LangFileInterceptor::class);
        }
    }

    /**
     * Load any drivers given by the package
     */
    private function loadDrivers()
    {
        Translate::pushDriver('null', function ($app, $config) {
            return new NullTranslator($config);
        });

        Translate::pushDriver('aws', function ($app, $config) {
            return new AWSTranslator($config, $app);
        });

        Translate::pushDriver('google-translate-free', function ($app, $config) {
            return new GoogleTranslateFreeTranslator($config, new GoogleTranslate());
        });

        Translate::pushDriver('deepl', function ($app, $config) {
            return new DeepLTranslator($config);
        });

        Translate::pushDriver('stack', function ($app, $config) {
            return new StackTranslator($config, $app->make(TranslationManager::class));
        });
    }

    /**
     * Parse any config
     *
     * Allow default driver and configurations to be defined in the config file. Loading them here provides users with
     * the option of defining configurations in the config file or directly in a service provider.
     */
    private function parseConfig()
    {
        foreach ($this->app->make(Config::class)->get('laravel-translate.configurations', []) as $name => $config) {
            Translate::pushConfiguration($name, $config[TranslationManager::DRIVER_KEY], $config);
        }

        Translate::setDefaultDriver($this->app->make(Config::class)->get('laravel-translate.default', 'null'));
    }

    private function defineBladeDirectives()
    {
        Blade::directive('trans', function ($expression) {
            return "<?php echo __t($expression); ?>";
        });
//
        Blade::directive('__t', function ($expression) {
            return "<?php echo {__t($expression)}; ?>";
        });

    }

}
