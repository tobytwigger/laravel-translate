<?php

namespace Twigger\Translate\Http\Controllers;

use Twigger\Translate\Http\Requests\TranslationControllerRequest;
use Twigger\Translate\Locale\Detect;
use Twigger\Translate\Translate;
use Illuminate\Http\Request;

class TranslationController
{

    public function translate(TranslationControllerRequest $request)
    {

        $lang = \Twigger\Translate\Detect::lang();
        $sourceLang = config('laravel-translate.default_language', 'en');



        return Translate::translate(
            $request->input('line'),
            $lang
        ) ?? $request->input('line');
    }

}
