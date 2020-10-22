<?php

namespace Twigger\Translate\Translate\Interceptors;

use Twigger\Translate\Translate\TranslationInterceptor;

/**
 * If the language requested is the same as the source language, we can just return the string(s)
 */
class SameLanguageInterceptor extends TranslationInterceptor
{

    /**
     * @inheritDoc
     */
    protected function canIntercept(string $line, string  $to, string $from): bool
    {
        return $to === $from;
    }

    /**
     * @inheritDoc
     */
    protected function get(string $line, string  $to, string $from): string
    {
        return $line;
    }

    /**
     * @inheritDoc
     */
    protected function save(string $line, string $to, string $from, string $translation): void
    {
        // No need to save anything
    }

    /**
     * Returns an array of booleans, delimiting which lines can be intercepted and which have to be translated
     *
     * You are welcome to override this method - by default, it will just call canIntercept many times
     *
     * @param array $lines An array of lines to check if interception is possible
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     *
     * @return array An array of booleans, corresponding to whether or not this interceptor can handle the line
     */
    protected function canInterceptMany(array $lines, string $to, string $from): array
    {
        return array_map(function($line) use ($to, $from) {
            return $to === $from;
        }, $lines);
    }

    /**
     * Get an array of translations
     *
     * You may override this method to provide an optimised implementation for retrieving multiple lines
     *
     * @param array $lines An array of lines to return the translation for
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     * @return array
     */
    protected function getMany(array $lines, string $to, string $from): array
    {
        return $lines;
    }

    /**
     * Save many translations to the interceptor
     *
     * @param array $lines Original lines of text
     * @param string $to Language to translate to
     * @param string $from Language to translate from
     * @param array $translations Translated lines of text to save
     * @return void
     */
    protected function saveMany(array $lines, string $to, string $from, array $translations): void
    {
        // No need to save
    }

}
