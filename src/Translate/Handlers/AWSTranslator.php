<?php

namespace Twigger\Translate\Translate\Handlers;

use Aws\Exception\AwsException;
use Aws\Sts\StsClient;
use Aws\Translate\TranslateClient;
use Illuminate\Contracts\Container\Container;
use Twigger\Translate\Translate\Translator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Use the AWS Translate service
 *
 * https://aws.amazon.com/translate/
 */
class AWSTranslator extends Translator
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
    public function __construct(array $config = [], Container $container)
    {
        parent::__construct($config);
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function translate(string $line, string $to, string $from): ?string
    {
        $config = $this->loadAWSConfiguration();

        $client = $this->container->make(TranslateClient::class, ['args' => $config]);
        try {
            $result = $client->translateText([
                'SourceLanguageCode' => $from,
                'TargetLanguageCode' => $to,
                'Text' => $line,
            ]);
            if ($result->hasKey('TranslatedText')) {
                return $result->get('TranslatedText');
            }
        } catch (AwsException $e) {
            if($this->getConfig('log_errors', true)) {
                Log::warning($e->getMessage());
            }
        }
        return null;
    }

    /**
     * Load the configuration to pass to AWS
     *
     * @return array
     */
    private function loadAWSConfiguration(): array
    {
        return [
            'version' => $this->getConfig('version'),
            'region' => $this->getConfig('region'),
            'credentials' => [
                'key' => $this->getConfig('key'),
                'secret' => $this->getConfig('secret')
            ]
        ];
    }
}
