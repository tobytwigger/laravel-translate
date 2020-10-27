<?php

namespace Twigger\Translate\Translate\Handlers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Twigger\Translate\Translate\Translator;

/**
 * Allow multiple translators to be used. If one translator fails, the next one will have a chance
 */
class StackTranslator extends Translator
{

    /**
     * @var Container
     */
    private $container;

    public function __construct(array $config = [], Container $container)
    {
        parent::__construct($config);
        $this->container = $container;
    }

    public function translate(string $line, string $to, string $from): ?string
    {
        foreach($this->getConfig('translators', []) as $translator)
        {
            try {
                $translation = $this->getTranslator($translator)
                    ->translate($line, $to, $from);
            } catch (BindingResolutionException $e) {
                $translation = null;
            }
            if($translation !== null) {
                return $translation;
            }
        }
        return null;
    }

    /**
     * Create a translator to use
     *
     * @param string $translator Class name of the translator
     * @return Translator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getTranslator(string $translator)
    {
        return $this->container->make($translator);
    }
}
