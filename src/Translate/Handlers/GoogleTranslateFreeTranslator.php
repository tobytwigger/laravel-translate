<?php

namespace Twigger\Translate\Translate\Handlers;

use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Twigger\Translate\Translate\Translator;

class GoogleTranslateFreeTranslator extends Translator
{

    /**
     * @var GoogleTranslate
     */
    private $googleTranslate;

    /**
     * @param array $config
     * @param GoogleTranslate $googleTranslate
     */
    public function __construct(array $config = [], GoogleTranslate $googleTranslate = null)
    {
        parent::__construct($config);
        if($googleTranslate === null) {
            throw new \Exception('The google translate instance must be passed to the google translate handler');
        }
        $this->googleTranslate = $googleTranslate;
    }

    public function translate(string $line, string $to, string $from): ?string
    {
        $this->googleTranslate->setSource($from);
        $this->googleTranslate->setTarget($to);
        try {
            return $this->googleTranslate->translate($line);
        } catch (\Exception $exception) {
            if($this->getConfig('log_errors', true)) {
                Log::warning($exception->getMessage());
            }
        }
        return null;
    }
}
