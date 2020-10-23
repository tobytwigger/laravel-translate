<?php

namespace Twigger\Translate\Translate;

/**
 * Defines the structure for an interceptor.
 *
 * An interceptor must extend this class, and should be able to return a translation without generating one.
 * An Interceptor will be given a chance to return a translation, and if it can't it will be given a chance to save the translation
 */
abstract class TranslationInterceptor extends Translator
{

    /**
     * @var Translator
     */
    private $translator;

    /**
     * Determine the action to take when a new translation is null
     *
     * If a translation could not be determined, and this is true, nothing will be saved.
     * If this is false, the saving of the translation will happen anyway
     *
     * @var bool
     */
    protected $ignoreNull = true;

    /**
     * @param array $config The configuration for the interceptor
     * @param Translator $translator The translator to retrieve the translation from
     */
    public function __construct(array $config, Translator $translator)
    {
        parent::__construct($config);
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function translate(string $line, string $to, string $from): ?string
    {
        if($this->canIntercept($line, $to, $from)) {
            return $this->get($line, $to, $from);
        }

        $result = $this->translator->translate($line, $to, $from);

        if($result !== null || !$this->ignoreNull) {
            $this->save($line, $to, $from, $result);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function translateMany(array $lines, string $to, string $from): array
    {
        $interceptionKeyMap = [];
        $translationKeyMap = [];
        $interceptions = [];
        $translations = [];

        foreach($this->canInterceptMany($lines, $to, $from) as $lineKey => $canIntercept) {
            if($canIntercept) {
                $interceptionKeyMap[] = $lineKey;
                $interceptions[] = $lines[$lineKey];
            } else {
                $translationKeyMap[] = $lineKey;
                $translations[] = $lines[$lineKey];
            }
        }

        if(count($translations) > 0) {
            $finishedTranslations = $this->translator->translateMany($translations, $to, $from);
            $finishedTranslationsToSave = array_filter($finishedTranslations, function($translation) {
                return $translation !== null || !$this->ignoreNull;
            });
            $this->saveMany(array_filter($translations, function($key) use ($finishedTranslationsToSave) {
                return array_key_exists($key, $finishedTranslationsToSave);
            }, ARRAY_FILTER_USE_KEY), $to, $from, $finishedTranslationsToSave);
        } else {
            $finishedTranslations = [];
        }
        if(count($interceptions) > 0) {
            $finishedInterceptions = $this->getMany($interceptions, $to, $from);
        } else {
            $finishedInterceptions = [];
        }

        foreach($interceptionKeyMap as $finishedKey => $actualKey) {
            $lines[$actualKey] = $finishedInterceptions[$finishedKey];
        }

        foreach($translationKeyMap as $finishedKey => $actualKey) {
            $lines[$actualKey] = $finishedTranslations[$finishedKey];
        }

        return $lines;
    }

    /**
     * Returns an array of booleans, delimiting which lines can be intercepted and which have to be translated
     *
     * You are welcome to override this method - by default, it will just call canIntercept many times
     *
     * @param array $lines An array of lines to check if interception is possible
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     *
     * @return array An array of booleans, corresponding to whether or not this interceptor can handle the line
     */
    protected function canInterceptMany(array $lines, string $to, string $from): array
    {
        return array_map(function($line) use ($to, $from) {
            return $this->canIntercept($line, $to, $from);
        }, $lines);
    }

    /**
     * Get an array of translations
     *
     * You may override this method to provide an optimised implementation for retrieving multiple lines
     *
     * @param array $lines An array of lines to return the translation for
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     * @return array
     */
    protected function getMany(array $lines, string $to, string $from): array
    {
        return array_map(function($line) use ($to, $from){
            return $this->get($line, $to, $from);
        }, $lines);
    }

    /**
     * Save many translations to the interceptor
     *
     * @param array $lines Original lines of text
     * @param string $to Language to translate to
     * @param string $from Language to translate from
     * @param array $translations Translated lines of text to save
     * @return void
     */
    protected function saveMany(array $lines, string $to, string $from, array $translations): void
    {
        foreach($lines as $key => $line) {
            $this->save($line, $to, $from, $translations[$key]);
        }
    }

    /**
     * Determine whether the given translation can be intercepted
     *
     * @param string $line The line in the original language
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     * @return bool If the interceptor can handle the translation
     */
    abstract protected function canIntercept(string $line, string $to, string $from): bool;

    /**
     * Get the translation for the given language
     *
     * @param string $line The line in the original language
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     * @return string|null The translation of the line
     */
    abstract protected function get(string $line, string $to, string $from): ?string;

    /**
     * Save the translation for the given language
     *
     * @param string $line The line in the original language
     * @param string $to The language to translate to
     * @param string $from The language to translate from
     * @param string $translation The translated string
     */
    abstract protected function save(string $line, string $to, string $from, string $translation): void;

}
