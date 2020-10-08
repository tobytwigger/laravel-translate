<?php

namespace Twigger\Translate\Locale;

/**
 * Register and retrieve detection strategies
 */
class DetectionStrategyStore
{

    /**
     * Holds the registered strategy class names
     *
     * @var array
     */
    protected $strategies = [];

    /**
     * Holds any strategies to prepend
     * @var array
     */
    protected $prepend = [];

    /**
     * Holds any strategies to append
     *
     * @var array
     */
    protected $append = [];

    /**
     * Register a strategy to use to resolve the requested language
     *
     * @param string $className Name of the class extending the DetectionStrategy abstract class
     */
    public function register(string $className): void
    {
        if(!array_key_exists($className, $this->strategies)) {
            $this->strategies[] = $className;
        }
    }

    /**
     * Register a strategy to use as a fallback
     *
     * @param string $className
     */
    public function registerLast(string $className): void
    {
        if(!array_key_exists($className, $this->strategies)) {
            $this->prepend[] = $className;
        }
    }

    /**
     * Register a strategy to use first
     *
     * @param string $className
     */
    public function registerFirst(string $className): void
    {
        if(!array_key_exists($className, $this->strategies)) {
            $this->append[] = $className;
        }
    }

    /**
     * Get all the registered detection strategies to use
     *
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->append, $this->strategies, $this->prepend);
    }

}
