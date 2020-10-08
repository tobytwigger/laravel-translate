<?php

namespace Twigger\Translate\Locale\Strategies;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Twigger\Translate\Locale\DetectionStrategy;
use Illuminate\Http\Request;

/**
 * Checks for language headers set in the request header
 */
class HeaderDetectionStrategy extends DetectionStrategy
{

    /**
     * The current request
     *
     * @var Request
     */
    private $request;

    /**
     * An array of ISO-639-1 language codes that are available to be found
     *
     * @var array
     */
    private $allowedLanguages;

    /**
     * @param Request $request
     * @param array $allowedLanguages An array of ISO-639-1 language codes that are available to be translated into
     */
    public function __construct(Request $request, array $allowedLanguages)
    {
        $this->request = $request;
        $this->allowedLanguages = $allowedLanguages;
    }

    /**
     * Get the locale requested by the user through browser headers
     *
     * Return the ISO-639-1 Code for the language, or null if locale not found
     *
     * @return string|null
     */
    protected function getCurrentLocale(): ?string
    {
        return $this->request->getPreferredLanguage(
            $this->orderedLanguages()
        );
    }

    /**
     * Order the available languages alphabetically
     *
     * @return array The available languages ordered alphabetically
     */
    private function orderedLanguages(): array
    {
        return (new Collection($this->allowedLanguages))
            ->sort()
            ->values()
            ->toArray();
    }
}
