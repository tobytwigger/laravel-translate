<?php

namespace Twigger\Translate\Translate\Handlers;

use BabyMarkt\DeepL\DeepL;
use BabyMarkt\DeepL\DeepLException;
use Exception;
use Illuminate\Contracts\Container\Container;
use Twigger\Translate\Translate\Translator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Use the AWS Translate service
 *
 * https://aws.amazon.com/translate/
 */
class DeepLTranslator extends Translator
{

    /**
     * The container to resolve a client from
     *
     * @var Container
     */
    private $container;

    /**
     * AWSTranslator constructor.
     * @param array $config
     * @param Container $container
     */
    public function __construct(array $config = [], Container $container = null)
    {
        parent::__construct($config);
        if ($container === null) {
            throw new Exception('The container instance must be passed to DeePL handler');
        }
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function translate(string $line, string $to, string $from): ?string
    {
        try {
            return $this->newDeepL()->translate($line, $from, $to)[0]['text'];
        } catch (DeepLException $exception) {
            if ($this->getConfig('log_errors', false)) {
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
        return new DeepL(
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
