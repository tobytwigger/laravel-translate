<?php

namespace Twigger\Tests\Translate\Unit\Translate\Handlers;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Handlers\NullTranslator;

class NullTranslatorTest extends TestCase
{

    /** @test */
    public function it_returns_null(){
        $translator = new NullTranslator([]);

        $this->assertNull($translator->translate('Line 1', 'en', 'fr'));
        $this->assertNull($translator->translate('Line 1', 'fr', 'en'));
        $this->assertNull($translator->translate('Line 1', 'ru', 'en_US'));
        $this->assertNull($translator->translate('Line 1', 'it', 'de'));
        $this->assertNull($translator->translate('Line 1', 'au', 'it'));
        $this->assertNull($translator->translate('Line 1', 'we', 'ru'));
    }

}
