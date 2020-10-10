<?php

namespace Twigger\Translate\Translate\Interceptors;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;
use Twigger\Translate\Translate\TranslationInterceptor;

class DatabaseInterceptor extends TranslationInterceptor
{

    protected $ignoreNull = false;

    public function canIntercept(string $line, string $to, string $from): bool
    {
        return TranslationModel::from($from)->to($to)->translate($line)->whereNotNull('text_translated')->count() > 0;
    }

    public function get(string $line, string $to, string $from): string
    {
        return TranslationModel::from($from)->to($to)->translate($line)->firstOrFail()->text_translated;
    }

    public function save(string $line, string $to, string $from, ?string $translation): void
    {
        TranslationModel::updateOrCreate([
            'id' => TranslationModel::getUniqueKey($line, $to, $from)
        ], [
            'text_original' => $line,
            'text_translated' => $translation,
            'lang_from' => $from,
            'lang_to' => $to
        ]);
    }
}
