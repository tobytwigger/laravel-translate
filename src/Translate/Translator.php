<?php

namespace Twigger\Translate\Translate;

use Illuminate\Support\Arr;

abstract class Translator
{

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Return the translation, or null if could not translate
     *
     * @param string $line
     * @param string $lang
     * @return string|null
     */
    abstract public function translate(string $line, string $lang): ?string;

    public function translateMany(array $lines, string $lang)
    {
        $translated = [];
        foreach($lines as $line) {
            $translated[] = $this->translate($line, $lang);
        }
        return $translated;
    }

}
