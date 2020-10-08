<?php

namespace Twigger\Translate\Locale;

/**
 * Defines a way of detecting the current language
 */
abstract class DetectionStrategy
{

    /**
     * The strategy to call after if locale could not be detected
     *
     * @var null|DetectionStrategy
     */
    private $successor = null;

    /**
     * Set the next strategy to call if locale could not be detected
     *
     * @param DetectionStrategy $strategy
     */
    public function setNext(DetectionStrategy $strategy)
    {
        $this->successor = $strategy;
    }

    /**
     * Detect the requested language
     *
     * @return string|null
     */
    public function detect(): ?string
    {
        $processed = $this->getCurrentLocale();

        if ($processed === null && $this->successor !== null) {
            $processed = $this->successor->detect();
        }

        return $processed;
    }

    /**
     * Get the locale requested by the user
     *
     * Return the ISO-639-1 Code for the language, or null if locale not found
     *
     * @return string|null
     */
    abstract protected function getCurrentLocale(): ?string;

}
