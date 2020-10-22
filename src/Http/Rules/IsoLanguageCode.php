<?php

namespace Twigger\Translate\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Test if the given value is an ISO-639-1 language code
 */
class IsoLanguageCode implements Rule
{

    /**
     * Check if the given value is a language code
     *
     * @param string $attribute The name of the attribute
     * @param mixed $value The value that should be a language code
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_string($value) && (strlen($value) === 2 || strlen($value) === 5);
    }

    /**
     * Customise the validation error message
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be an ISO-639-1 language code';
    }
}
