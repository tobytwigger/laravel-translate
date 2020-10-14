<?php

namespace Twigger\Translate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Twigger\Translate\Detect;
use Twigger\Translate\Http\Rules\IsoLanguageCode;

/**
 * The request for the translation API
 */
class TranslationControllerRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'line' => [
                'required_without:lines', 'string',
                function ($attribute, $value, $fail) {
                    if($this->has('lines')) {
                        $fail('Only one of line or lines may be given');
                    }
                }
            ],
            'lines' => [
                'required_without:line', 'array',
                function ($attribute, $value, $fail) {
                    if($this->has('line')) {
                        $fail('Only one of line or lines may be given');
                    }
                }
            ],
            'lines.*' => 'string',
            'target_lang' => ['required', new IsoLanguageCode],
            'source_lang' => ['required', new IsoLanguageCode]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Add the target language to the request
     * Add the source language to the request
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'target_lang' => $this->input('target_lang', Detect::lang()),
            'source_lang' => $this->input('source_lang', config('laravel-translate.default_language'))
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function messages()
    {
        return [
            'line.required_without' => 'Either the line or lines key must be given',
            'lines.required_without' => 'Either the line or lines key must be given',
            'lines.*.string' => 'The line to translate must be a string',

            'line.string' => 'A string must be given',
            'lines.array' => 'An array must be given',
            'target_lang.required' => 'The target language must be an ISO-639-1 language code',
            'source_lang.required' => 'The source language must be an ISO-639-1 language code'
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return [
            'target_lang' => 'target language',
            'source_lang' => 'source language'
        ];
    }

}
