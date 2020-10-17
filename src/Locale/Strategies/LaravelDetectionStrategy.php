<?php

namespace Twigger\Translate\Locale\Strategies;

use Illuminate\Contracts\Foundation\Application;
use Twigger\Translate\Locale\DetectionStrategy;
use Illuminate\Http\Request;

/**
 * Get the language from the laravel app. This is usually set through the config.
 */
class LaravelDetectionStrategy extends DetectionStrategy
{

    /**
     * The app to get the locale from
     *
     * @var Application
     */
    private $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
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
        return $this->application->getFallbackLocale();
    }
}
