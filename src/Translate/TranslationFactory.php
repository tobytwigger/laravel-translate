<?php

namespace Twigger\Translate\Translate;

class TranslationFactory
{

    private array $interceptor = [];

    public function intercept(string $interceptor)
    {
        $this->interceptor[] = $interceptor;
    }

    public function create(Translator $translator)
    {
        foreach(array_reverse($this->interceptor) as $interceptor) {
            $translator = app($interceptor, ['config' => [], 'translator' => $translator]);
        }
        return $translator;
    }

}
