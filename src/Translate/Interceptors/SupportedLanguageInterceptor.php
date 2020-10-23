<?php

namespace Twigger\Translate\Translate\Interceptors;

use Illuminate\Support\Arr;
use Twigger\Translate\Translate\TranslationInterceptor;

class SupportedLanguageInterceptor extends TranslationInterceptor
{

    /**
     * Supported languages
     *
     * @var array
     */
    private static $supported = [];

    /**
     * We can interept if the language is NOT supported
     *
     * @inheritDoc
     */
    protected function canIntercept(string $line, string $to, string $from): bool
    {
        return !empty(static::$supported) && !in_array($to, static::$supported);
    }

    /**
     * Return null to mark translation not supported
     *
     * @inheritDoc
     */
    protected function get(string $line, string $to, string $from): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    protected function save(string $line, string $to, string $from, string $translation): void
    {
        // No saving needed
    }

    /**
     * Support a language or languages
     *
     * @param array $languages
     */
    public static function support($languages = []): void
    {
        static::$supported = array_merge(static::$supported, Arr::wrap($languages));
    }

    /**
     * Revoke support of a language
     *
     * @param array|string $languages An array of languages or a single language
     */
    public static function revoke($languages = []): void
    {
        static::$supported = array_diff(static::$supported, Arr::wrap($languages));
    }

    /**
     * Support all languages
     */
    public static function supportAll(): void
    {
        static::$supported = [];
    }
}
