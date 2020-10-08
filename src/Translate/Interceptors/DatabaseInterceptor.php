<?php

namespace Twigger\Translate\Translate\Interceptors;

use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;
use Twigger\Translate\Translate\TranslationInterceptor;

class DatabaseInterceptor extends TranslationInterceptor
{

    private array $rows = [];

    private function getRows(array $lines, string $lang)
    {
        if(empty($this->rows)) {
            $this->rows = TranslationModel::whereIn('original', $lines)
                ->where('lang', $lang)->toArray();
        }
        return $this->rows;
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
            return $this->canIntercept($line, $lang);
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
        return array_map(function($line) use ($lang) {
            return $this->get($line, $lang);
        }, $lines);
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
            TranslationModel::create([
                'original' => $line,
                'translation' => $translations[$key],
                'lang' => $lang
            ]);
        }
    }

    public function canIntercept(string $line, string $lang): bool
    {
        return count(array_filter($this->rows, function($row) use ($line, $lang) {
            return array_key_exists('original', $row) && $row['original'] === $line
                && array_key_exists('lang', $row) && $row['lang'] === $lang
                && array_key_exists('translation', $row) && $row['translation'] !== null;
        })) > 0;
    }

    public function get(string $line, string $lang): string
    {
        foreach($this->rows as $row) {
            if(array_key_exists('original', $row) && $row['original'] === $line
                && array_key_exists('lang', $row) && $row['lang'] === $lang
                && array_key_exists('translation', $row) && $row['translation'] !== null) {
                return $row['translation'];
            }
        }
    }

    public function save(string $line, string $lang, string $translation): void
    {
        TranslationModel::create([
            'original' => $line,
            'translation' => $translation,
            'lang' => $lang
        ]);
    }
}
