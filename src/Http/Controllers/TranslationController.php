<?php

namespace Twigger\Translate\Http\Controllers;

use Illuminate\Http\Response;
use Twigger\Translate\Http\Requests\TranslationControllerRequest;
use Twigger\Translate\Locale\Detect;
use Twigger\Translate\Translate;
use Illuminate\Http\Request;
use Twigger\Translate\Translate\TranslationManager;

class TranslationController
{

    /**
     * @var TranslationManager
     */
    private $translationManager;

    public function __construct(TranslationManager $translationManager)
    {
        $this->translationManager = $translationManager;
    }

    public function translate(TranslationControllerRequest $request)
    {
        $targetLang = $request->input('target_lang');
        $sourceLang = $request->input('source_lang', config('laravel-translate.default_language', 'en'));

        if($request->has('line')){
            $response['translation'] = $this->translateLine(
                $request->input('line'), $targetLang, $sourceLang
                ) ?? $request->input('line');
        } else {
            $response['translations'] = $this->translateLine(
                    $request->input('lines'), $targetLang, $sourceLang
                ) ?? $request->input('lines');
        }
        return new Response($response, 200);
    }

    private function translateLine(string $line, string $targetLang, string $sourceLang)
    {
        return $this->translationManager->driver(null)->translate($line, $targetLang, $sourceLang);
    }

    private function translateLines(array $lines, string $targetLang, string $sourceLang)
    {
        return $this->translationManager->driver(null)->translateMany($lines, $targetLang, $sourceLang);
    }

}
