<?php

namespace Twigger\Translate\Locale;

/**
 * Provides an interface to the underlying detection logic
 */
class Detector
{

    /**
     * Holds the detection strategy to proxy
     *
     * @var DetectionStrategy
     */
    private $detectionStrategy;

    public function __construct(DetectionStrategy $detectionStrategy)
    {
        $this->detectionStrategy = $detectionStrategy;
    }

    /**
     * Get the requested language for the environment
     *
     * @return string|null The ISO-639-1 Code for the language wanted by the user
     */
    public function lang()
    {
        return $this->detectionStrategy->detect();
    }

}
