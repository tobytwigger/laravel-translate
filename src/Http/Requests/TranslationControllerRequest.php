<?php

namespace Twigger\Translate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Twigger\Translate\Detect;

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
            'line' => 'required_without:lines|string',
            'lines' => 'required_without:line|array',
            'lines.*' => 'string',
            'target_lang' => 'required|iso_language_code',
            'source_lang' => 'sometimes|iso_language_code'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Add the target language to the request
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'target_lang' => $this->input('target_lang', Detect::lang())
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



}
