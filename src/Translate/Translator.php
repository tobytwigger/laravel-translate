<?php

namespace Twigger\Translate\Translate;

use Illuminate\Support\Arr;

/**
 * Defines the structure for a class containing translation logic
 *
 * Any translator service must extend this class
 */
abstract class Translator
{

    /**
     * Configuration for the translator
     *
     * @var array
     */
    protected $config;

    /**
     * @param array $config Configuration for the translator
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Retrieve a configuration value
     *
     * @param string $key The key of the configuration. You may use dot notation to access multiple levels of array
     * @param mixed $default The default value to return if the key is not found
     *
     * @return mixed
     */
    protected function getConfig(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Return the translation, or return null if could not translate
     *
     * @param string $line The line to translate
     * @param string $to The ISO-639-1 code for the language to translate to
     * @param string $from The ISO-639-1 code for the language to translate from
     * @return string|null The translated string, or null if the translation was not possible
     */
    abstract public function translate(string $line, string $to, string $from): ?string;

    /**
     * Translate many lines from one language to another
     *
     * This function is optional, but when implemented can improve a service speed. If the service allows for multiple
     * strings to be translated at the same time, implement the method here to optimise use of the service.
     *
     * @param array $lines An array of lines to translate
     * @param string $to The ISO-639-1 code for the language to translate to
     * @param string $from The ISO-639-1 code for the language to translate from
     * @return array An array of translated lines, with the index corresponding to the index of $lines (i.e. same order)
     */
    public function translateMany(array $lines, string $to, string $from): array
    {
        $translated = [];
        foreach($lines as $line) {
            $translated[] = $this->translate($line, $to, $from);
        }
        return $translated;
    }

}
