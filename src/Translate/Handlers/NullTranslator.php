<?php

namespace Twigger\Translate\Translate\Handlers;

use Twigger\Translate\Translate\Translator;

/**
 * Turns off dynamic translations
 */
class NullTranslator extends Translator
{

    public function translate(string $line, string $to, string $from): ?string
    {
        return null;
    }
}
