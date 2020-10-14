<?php

namespace Twigger\Translate\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsoLanguageCode implements Rule
{

    public function passes($attribute, $value)
    {
        return is_string($value) && (strlen($value) === 2 || strlen($value) === 5);
    }

    public function message()
    {
        return 'The :attribute must be an ISO-639-1 language code';
    }
}
