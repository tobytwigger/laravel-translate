<?php

namespace Twigger\Translate\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Response;
use Twigger\Translate\Http\Requests\TranslationControllerRequest;
use Twigger\Translate\Translate\TranslationManager;

/**
 * An API for using the translator
 */
class TranslationController
{

    /**
     * Holds the translation service
     *
     * @var TranslationManager
     */
    private $translationManager;

    /**
     * @param TranslationManager $translationManager
     */
    public function __construct(TranslationManager $translationManager)
    {
        $this->translationManager = $translationManager;
    }

    /**
     * Translate the given string(s)
     *
     * @param TranslationControllerRequest $request
     * @return Response
     */
    public function translate(TranslationControllerRequest $request)
    {
        $response = $this->handleTranslation($request);

        return new Response($response, 200);
    }

    /**
     * Handle the translation
     *
     * @param TranslationControllerRequest $request
     * @return array
     * @throws \Exception
     */
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
