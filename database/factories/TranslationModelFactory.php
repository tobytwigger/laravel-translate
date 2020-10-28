<?php

namespace Database\Factories;

use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TranslationModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text_original' => $this->faker->sentence,
            'text_translated' => $this->faker->sentence,
            'lang_from' => $this->faker->languageCode,
            'lang_to' => $this->faker->languageCode
        ];
    }
}
