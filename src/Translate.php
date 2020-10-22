<?php

namespace Twigger\Translate;

use Twigger\Translate\Translate\Translator;
use Twigger\Translate\Translate\TranslationManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string translate(string $line, string $to, string $from) Translate a single line
 * @method static string translateMany(array $line, string $to, string $from) Translate an array of lines
 * @method static Translator driver(string $driver) Retrieve a specific translation driver
 *
 * @method static void pushDriver(string $driver, \Closure $callback) Push the given driver and create it with the given callback
 * @method static void pushConfiguration(string $name, string $driver, array $configuration) Push the given configuration
 * @method static void setDefaultDriver(string $name) Set the default configuration to use
 *
 * @see TranslationManager
 */
class Translate extends Facade
{

    /**
     * Retrieve the key the TranslationManager is bound to
     *
     * @return string The facade accessor
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-translate';
    }

}
