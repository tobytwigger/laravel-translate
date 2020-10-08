<?php

namespace Twigger\Translate\Locale\Strategies;

use Twigger\Translate\Locale\DetectionStrategy;
use Illuminate\Http\Request;

/**
 * Get the language from the request body
 */
class BodyDetectionStrategy extends DetectionStrategy
{

    /**
     * The request with the input
     *
     * @var Request
     */
    private $request;

    /**
     * The request input key to retrieve
     *
     * @var string
     */
    private $requestKey;

    public function __construct(Request $request, string $requestKey)
    {
        $this->request = $request;
        $this->requestKey = $requestKey;
    }

    /**
     * Get the locale requested by the user
     *
     * Return the ISO-639-1 Code for the language, or null if locale not found
     *
     * @return string|null
     */
    protected function getCurrentLocale(): ?string
    {
        return $this->request->input($this->requestKey, null);
    }
}
