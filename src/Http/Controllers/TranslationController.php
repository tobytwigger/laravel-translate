<?php

namespace Twigger\Translate\Http\Controllers;

use Twigger\Translate\Locale\Detect;
use Twigger\Translate\Translate;
use Illuminate\Http\Request;

class TranslationController
{

    public function translate(Request $request)
    {
        dd(Translate::translate(            $request->input('line'), 'fr'));
        $lang = \Twigger\Translate\Detect::lang();
        if ($lang === null) {
            return 'no';
        }
        return Translate::translate(
            $request->input('line'),
            $lang
        ) ?? $request->input('line');
    }

}
