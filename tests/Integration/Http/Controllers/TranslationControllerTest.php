<?php

namespace Twigger\Tests\Translate\Integration\Http\Controllers;

use Twigger\Tests\Translate\LaravelTestCase;

class TranslationControllerTest extends LaravelTestCase
{

    /** @test */
    public function it_translates_using_a_single_line_given(){
        $response = $this->postJson('_translate');
        dd($response->getContent());
    }

    /** @test */
    public function it_translates_using_an_array_of_lines(){

    }

    /** @test */
    public function it_takes_the_source_language_from_the_config(){

    }

}
