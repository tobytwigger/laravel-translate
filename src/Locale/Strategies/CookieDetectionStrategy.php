<?php

namespace Twigger\Translate\Locale\Strategies;

use Twigger\Translate\Locale\DetectionStrategy;
use Illuminate\Http\Request;

/**
 * Detect a language cookie
 */
class CookieDetectionStrategy extends DetectionStrategy
{

    /**
     * Requests that holds the cookies
     *
     * @var Request
     */
    private $request;

    /**
     * The key of the cookie
     *
     * @var string
     */
    private $cookieKey;

    public function __construct(Request $request, string $cookieKey)
    {
        $this->request = $request;
        $this->cookieKey = $cookieKey;
    }

    /**
     * Get the locale requested by the user through a cookie
     *
     * Return the ISO-639-1 Code for the language, or null if locale not found
     *
     * @return string|null
     */
    protected function getCurrentLocale(): ?string
    {
        return $this->request->cookie($this->cookieKey, null);
    }
}
