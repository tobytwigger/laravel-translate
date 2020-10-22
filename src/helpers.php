<?php

if(!function_exists('laravelTranslate')) {
    /**
     * Translate a line
     *
     * @param string|null $line Line to translate. Null to return the translation manager
     * @param string|null $to
     * @param string|null $from
     * @return string|\Twigger\Translate\Translate\TranslationManager|null
     * @throws Exception
     */
    function laravelTranslate(string $line = null, string $to = null, string $from = null) {
        /** @var \Twigger\Translate\Translate\TranslationManager $translator */
        $translator = app('laravel-translate');

        if($line === null) {
            return $translator;
        }

        return $translator->translate(
            $line,
            $to ?? \Twigger\Translate\Detect::lang(),
            $from ?? config('app.locale')
        );

    }


}

if(!function_exists('__t')) {
    /**
     * Translate a line
     *
     * @param string|null $line
     * @param string|null $to
     * @param string|null $from
     * @return string|\Twigger\Translate\Translate\TranslationManager|null
     * @throws Exception
     */
    function __t(string $line = null, string $to = null, string $from = null) {
        return laravelTranslate($line, $to, $from);
    }


}
