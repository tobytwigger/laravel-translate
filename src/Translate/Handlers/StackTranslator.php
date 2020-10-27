<?php

namespace Twigger\Translate\Translate\Handlers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

/**
 * Allow multiple translators to be used. If one translator fails, the next one will have a chance
 */
class StackTranslator extends Translator
{

    /**
     * @var TranslationManager
     */
    private $translationManager;

    public function __construct(array $config = [], TranslationManager $translationManager)
    {
        parent::__construct($config);
        $this->translationManager = $translationManager;
    }

    public function translate(string $line, string $to, string $from): ?string
    {
        foreach($this->getConfig('translators', []) as $translator)
        {
            try {
                $translation = $this->translationManager->driver($translator)
                    ->translate($line, $to, $from);
            } catch (\Exception $e) {
                $translation = null;
            }
            if($translation !== null) {
                return $translation;
            }
        }
        return null;
    }

}
