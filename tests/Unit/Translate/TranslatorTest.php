<?php

namespace Twigger\Tests\Translate\Unit\Translate;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Translator;

class TranslatorTest extends TestCase
{

    /** @test */
    public function getConfig_returns_the_value_for_the_given_key(){
        $translator = new TranslatorTestDummyTranslator(['key1' => 'val1', 'key2' => 'val2']);

        $this->assertEquals('val1', $translator->publicGetConfig('key1'));
        $this->assertEquals('val2', $translator->publicGetConfig('key2'));

    }

    /** @test */
    public function getConfig_can_use_dot_notation_for_arrays(){
        $translator = new TranslatorTestDummyTranslator([
            'key1' => [
                'subkey1' => 'val1'
            ],
            'key2' => [
                'subkey2' => [
                    'subkey3' => 'val2'
                ],
            ]
        ]);

        $this->assertEquals('val1', $translator->publicGetConfig('key1.subkey1'));
        $this->assertEquals(['subkey3' => 'val2'], $translator->publicGetConfig('key2.subkey2'));
    }

    /** @test */
    public function getConfig_returns_the_default_value_if_key_not_found(){
        $translator = new TranslatorTestDummyTranslator([]);

        $this->assertEquals('default1', $translator->publicGetConfig('key1', 'default1'));
    }

    /** @test */
    public function getConfig_default_defaults_to_null(){
        $translator = new TranslatorTestDummyTranslator([]);

        $this->assertNull($translator->publicGetConfig('key1'));
    }


    /** @test */
    public function translateMany_by_default_calls_translate_n_times(){
        $lines = [
            'This is a test',
            'This is another test',
            'This is a final test'
        ];

        $translator = new TranslatorTestDummyTranslator([]);
        $this->assertEquals([
            'This is a test from en to ru',
            'This is another test from en to ru',
            'This is a final test from en to ru'
        ], $translator->translateMany($lines, 'ru', 'en'));
    }

}

class TranslatorTestDummyTranslator extends Translator
{

    public function publicGetConfig(string $key, $default = null)
    {
        return $this->getConfig($key, $default);
    }

    public function translate(string $line, string $to, string $from): ?string
    {
        return $line . ' from ' . $from . ' to ' . $to;
    }
}
