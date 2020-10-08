<?php

namespace Twigger\Translate\Translate\Interceptors;

use Twigger\Translate\Translate\TranslationInterceptor;
use Twigger\Translate\Translate\Translator;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;

class CacheInterceptor extends TranslationInterceptor
{

    /**
     * @var Repository
     */
    private Repository $cache;

    public function __construct(array $config, Translator $translator, Repository $cache)
    {
        parent::__construct($config, $translator);
        $this->cache = $cache;
    }

    /**
     * Returns an array of booleans, delimiting which lines can be intercepted and which have to be translted
     *
     * @param array $lines
     * @param string $lang
     * @return array
     */
    public function canInterceptMany(array $lines, string $lang): array
    {
        return array_map(function($line) use ($lang) {
            return $this->cache->has($this->getKey($line, $lang));
        }, $lines);
    }

    /**
     * Get an array of translations
     *
     * @param array $lines
     * @param string $lang
     * @return array
     */
    public function getMany(array $lines, string $lang): array
    {
        return (array) $this->cache->getMultiple(
            array_map(function($line) use ($lang) {
                return $this->getKey($line, $lang);
            }, $lines)
        );
    }

    /**
     * Save many translations
     *
     * @param array $lines
     * @param string $lang
     * @param array $translation
     * @return array
     */
    public function saveMany(array $lines, string $lang, array $translations): void
    {
        foreach($lines as $key => $line) {
            $this->cache->forever($this->getKey($line, $lang), $translations[$key]);
        }
    }

    public function canIntercept(string $line, string $lang): bool
    {
        return $this->cache->has($this->getKey($line, $lang));
    }

    public function get(string $line, string $lang): string
    {
        return $this->cache->get($this->getKey($line, $lang), null);
    }

    public function save(string $line, string $lang, string $translation): void
    {
        $this->cache->forever($this->getKey($line, $lang), $translation);
    }

    private function getKey(string $line, string $lang)
    {
        return md5(Cache::class . $line . $lang);
    }
}
