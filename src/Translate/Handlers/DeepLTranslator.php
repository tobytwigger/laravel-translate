<?php

namespace Twigger\Translate\Translate\Handlers;

use BabyMarkt\DeepL\DeepL;
use BabyMarkt\DeepL\DeepLException;
use Twigger\Translate\Translate\Translator;
use Illuminate\Support\Facades\Log;

/**
 * Use the DeepL service
 *
 */
class DeepLTranslator extends Translator
{

    /**
     * @var DeepL|null
     */
    private $deepL;

    public function __construct(array $config = [], DeepL $deepL = null)
    {
        parent::__construct($config);
        $this->deepL = $deepL;
    }

    /**
     * @inheritDoc
     */
    public function translate(string $line, string $to, string $from): ?string
    {
        try {
            return $this->newDeepL()->translate($line, $from, $to)[0]['text'];
        } catch (DeepLException $exception) {
            if ($this->getConfig('log_errors', true)) {
                Log::warning($exception->getMessage());
            }
        }
        return null;
    }

    /**
     * Load the configuration to pass to AWS
     *
     * @return DeepL
     */
    private function newDeepL(): DeepL
    {
        return $this->deepL ?? new DeepL(
            $this->getConfig('auth_key'),
            $this->getConfig('api_version'),
            $this->getConfig('host'));
    }

    public function translateMany(array $lines, string $to, string $from): array
    {
        try {
            return array_map(function ($translatedLines) {
                return $translatedLines['text'];
            }, $this->newDeepL()->translate($lines, $from, $to));
        } catch (DeepLException $exception) {
            if ($this->getConfig('log_errors', false)) {
                Log::warning($exception->getMessage());
            }
        }
        return array_fill(0, count($lines), null);
    }
}
