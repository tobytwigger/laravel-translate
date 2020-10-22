<?php

namespace Twigger\Tests\Translate\Integration\Locale\Strategies;

use Illuminate\Http\Request;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\Strategies\HeaderDetectionStrategy;

class HeaderDetectionStrategyTest extends TestCase
{

    /** @test */
    public function getCurrentLocale_returns_the_preferred_locale_when_available_languages_are_empty()
    {
        $request = Request::create('/test', 'POST', [], [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'en_US;q=0.5,en_GB;q=0.9,fr'
        ], '');
        $headerDetectionStrategy = new HeaderDetectionStrategy($request, []);

        $this->assertEquals('fr', $headerDetectionStrategy->detect());
    }

    /** @test */
    public function getCurrentLocale_obeys_available_locales()
    {
        $request = Request::create('/test', 'POST', [], [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'en_US;q=0.5,en_GB,fr;q=0.01'
        ], '');
        $headerDetectionStrategy = new HeaderDetectionStrategy($request, ['fr', 'en_US']);

        $this->assertEquals('en_US', $headerDetectionStrategy->detect());
    }


}
