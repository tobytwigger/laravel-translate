<?php

namespace Twigger\Translate\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
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
    /**
     * @var Repository
     */
    private $config;

    /**
     * TranslationController constructor.
     * @param TranslationManager $translationManager
     * @param Repository $config
     */
    public function __construct(TranslationManager $translationManager, Repository $config)
    {
        $this->translationManager = $translationManager;
        $this->config = $config;
    }

    public function translate(TranslationControllerRequest $request)
    {

        $response = $this->handleTranslation($request);

        return new Response($response, 200);
    }

    private function handleTranslation(TranslationControllerRequest $request)
    {
        $response = [];

        $targetLang = $request->input('target_lang');
        $sourceLang = $request->input('source_lang');

        if($request->has('line')){
            $response['translation'] = $this->translationManager->driver()->translate(
                    $request->input('line'), $targetLang, $sourceLang
                ) ?? $request->input('line');
        } else if($request->has('lines')) {
            $response['translations'] = collect($this->translationManager->driver()->translateMany(
                $request->input('lines'), $targetLang, $sourceLang
            ))->map(function($translation, $index) use ($request) {
                return $translation ?? $request->input('lines')[$index];
            })->toArray();
        }

        return $response;
    }

}
