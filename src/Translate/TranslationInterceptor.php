<?php

namespace Twigger\Translate\Translate;

abstract class TranslationInterceptor extends Translator
{

    /**
     * @var Translator
     */
    private Translator $translator;

    public function __construct(array $config, Translator $translator)
    {
        parent::__construct($config);
        $this->translator = $translator;
    }

    public function translate(string $line, string $to, string $from): ?string
    {
        if($this->canIntercept($line, $lang)) {
            return $this->get($line, $lang);
        }

        $result = $this->translator->translate($line, $lang);

        if($result !== null) {
            $this->save($line, $lang, $result);
        }

        return $result;
    }

    public function translateMany(array $lines, string $to, string $from): array
    {
        $interceptionKeyMap = [];
        $translationKeyMap = [];
        $interceptions = [];
        $translations = [];

        foreach($this->canInterceptMany($lines, $lang) as $lineKey => $canIntercept) {
            if($canIntercept) {
                $interceptionKeyMap[] = $lineKey;
                $interceptions[] = $lines[$lineKey];
            } else {
                $translationKeyMap[] = $lineKey;
                $translations[] = $lines[$lineKey];
            }
        }

        $finishedTranslations = $this->translator->translateMany($translations, $lang);
        $finishedInterceptions = $this->getMany($interceptions, $lang);

        $this->saveMany($translations, $lang, $finishedTranslations);

        foreach($interceptionKeyMap as $finishedKey => $actualKey) {
            $lines[$actualKey] = $finishedInterceptions[$finishedKey];
        }

        foreach($translationKeyMap as $finishedKey => $actualKey) {
            $lines[$actualKey] = $finishedTranslations[$finishedKey];
        }

        return $lines;
    }

    /**
     * Returns an array of booleans, delimiting which lines can be intercepted and which have to be translted
     *
     * @param array $lines
     * @param string $lang
     * @return array
     */
    abstract public function canInterceptMany(array $lines, string $lang): array;

    /**
     * Get an array of translations
     *
     * @param array $lines
     * @param string $lang
     * @return array
     */
    abstract public function getMany(array $lines, string $lang): array;

    /**
     * Save many translations
     *
     * @param array $lines
     * @param string $lang
     * @param array $translation
     * @return array
     */
    abstract public function saveMany(array $lines, string $lang, array $translations): void;

    abstract public function canIntercept(string $line, string $lang): bool;

    abstract public function get(string $line, string $lang): string;

    abstract public function save(string $line, string $lang, string $translation): void;

    // Methods to catch and return line, and one to save it.
}
