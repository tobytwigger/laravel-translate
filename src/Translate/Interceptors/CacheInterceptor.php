<?php

namespace Twigger\Translate\Translate\Interceptors;

use Twigger\Translate\Translate\TranslationInterceptor;
use Twigger\Translate\Translate\Translator;
use Illuminate\Contracts\Cache\Repository;

/**
 * Saves and retrieves translations from the cache
 */
class CacheInterceptor extends TranslationInterceptor
{

    /**
     * Holds the underlying cache instance
     * @var Repository
     */
    private $cache;

    /**
     * @param array $config Config for the cache
     * @param Translator $translator The underlying translator
     * @param Repository $cache The cache to save and retrieve translations
     */
    public function __construct(array $config, Translator $translator, Repository $cache)
    {
        parent::__construct($config, $translator);
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getMany(array $lines, string $to, string $from): array
    {
        return array_values((array) $this->cache->getMultiple(
            array_map(function($line) use ($to, $from) {
                return static::getCacheKey($line, $to, $from);
            }, $lines)
        ));
    }

    /**
     * @inheritDoc
     */
    protected function canIntercept(string $line, string  $to, string $from): bool
    {
        return $this->cache->has(static::getCacheKey($line, $to, $from));
    }

    /**
     * @inheritDoc
     */
    protected function get(string $line, string  $to, string $from): string
    {
        return $this->cache->get(static::getCacheKey($line, $to, $from), null);
    }

    /**
     * @inheritDoc
     */
    protected function save(string $line, string $to, string $from, string $translation): void
    {
        $this->cache->forever(static::getCacheKey($line, $to, $from), $translation);
    }

    /**
     * Create a unique key for the cache given the arguments
     *
     * @param string $line The original line to translate
     * @param string $to The language to translate to
     * @param string $from The original line language
     *
     * @return string A unique key
     */
    public static function getCacheKey(string $line, string  $to, string $from): string
    {
        return md5(strtolower(CacheInterceptor::class . $line .  $to . $from));
    }
}
