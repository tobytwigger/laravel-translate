<?php

namespace Twigger\Translate\Translate\Interceptors;

use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;
use Twigger\Translate\Translate\TranslationInterceptor;

/**
 * An interceptor that checks the database for any overridden translations
 */
class DatabaseInterceptor extends TranslationInterceptor
{

    /**
     * Mark this interceptor as one that wants to know about values that weren't translated successfully
     *
     * @var bool
     */
    protected $ignoreNull = false;

    /**
     * Detect if the line being translated has an override in the database
     *
     * @param string $line The line to check
     * @param string $to The target language
     * @param string $from The source language
     *
     * @return bool
     */
    public function canIntercept(string $line, string $to, string $from): bool
    {
        return TranslationModel::from($from)->to($to)->translate($line)->whereNotNull('text_translated')->count() > 0;
    }

    /**
     * Get the translated value from the database
     *
     * @param string $line The line to get the translation for
     * @param string $to The target language
     * @param string $from The source language
     * @return string
     */
    public function get(string $line, string $to, string $from): string
    {
        return TranslationModel::from($from)->to($to)->translate($line)->firstOrFail()->text_translated;
    }

    /**
     * @param string $line The line to check
     * @param string $to The target language
     * @param string $from The source language
     * @param string|null $translation The translation, or null if the translation wasn't possible
     */
    public function save(string $line, string $to, string $from, ?string $translation): void
    {
        TranslationModel::updateOrCreate([
            'id' => TranslationModel::getUniqueKey($line, $to, $from)
        ], [
            'text_original' => $line,
            'text_translated' => $translation,
            'lang_from' => $from,
            'lang_to' => $to
        ]);
    }
}
