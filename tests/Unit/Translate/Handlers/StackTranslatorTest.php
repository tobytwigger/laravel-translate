<?php

namespace Twigger\Tests\Translate\Unit\Translate\Handlers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Handlers\StackTranslator;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

class StackTranslatorTest extends TestCase
{

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $translator1;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $translator2;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $translator3;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $translationManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator1 = $this->prophesize(Translator::class);
        $this->translator2 = $this->prophesize(Translator::class);
        $this->translator3 = $this->prophesize(Translator::class);

        $this->translationManager = $this->prophesize(TranslationManager::class);
        $this->translationManager->driver('driver1')->willReturn($this->translator1->reveal());
        $this->translationManager->driver('driver2')->willReturn($this->translator2->reveal());
        $this->translationManager->driver('driver3')->willReturn($this->translator3->reveal());
    }

    /** @test */
    public function it_returns_the_translation_if_one_translator_used(){
        $this->translator1->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn('Line 1 translated');

        $stackTranslator = new StackTranslator([
            'translators' => ['driver1']
        ], $this->translationManager->reveal());

        $this->assertEquals('Line 1 translated',
            $stackTranslator->translate('Line 1', 'fr', 'en')
        );
    }

    /** @test */
    public function it_returns_null_if_one_translator_used_that_returns_null(){
        $this->translator1->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);

        $stackTranslator = new StackTranslator([
            'translators' => ['driver1']
        ], $this->translationManager->reveal());

        $this->assertNull(
            $stackTranslator->translate('Line 1', 'fr', 'en')
        );
    }

    /** @test */
    public function it_returns_the_second_translation_if_the_first_returns_null(){
        $this->translator1->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);
        $this->translator2->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn('Line 1 translated');
        $this->translator3->translate('Line 1', 'fr', 'en')->shouldNotBeCalled();

        $stackTranslator = new StackTranslator([
            'translators' => ['driver1', 'driver2', 'driver3']
        ], $this->translationManager->reveal());

        $this->assertEquals('Line 1 translated',
            $stackTranslator->translate('Line 1', 'fr', 'en')
        );
    }

    /** @test */
    public function it_returns_the_third_translation_if_the_first_and_second_return_null(){
        $this->translator1->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);
        $this->translator2->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);
        $this->translator3->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn('Line 1 translated');

        $stackTranslator = new StackTranslator([
            'translators' => ['driver1', 'driver2', 'driver3']
        ], $this->translationManager->reveal());

        $this->assertEquals('Line 1 translated',
            $stackTranslator->translate('Line 1', 'fr', 'en')
        );
    }

    /** @test */
    public function it_returns_null_if_three_translators_return_null(){
        $this->translator1->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);
        $this->translator2->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);
        $this->translator3->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);

        $stackTranslator = new StackTranslator([
            'translators' => ['driver1', 'driver2', 'driver3']
        ], $this->translationManager->reveal());

        $this->assertNull(
            $stackTranslator->translate('Line 1', 'fr', 'en')
        );
    }

    /** @test */
    public function it_skips_a_translator_if_it_is_not_resolved(){
        $this->translator1->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn(null);
        $this->translator2->translate('Line 1', 'fr', 'en')->shouldNotBeCalled();
        $this->translator3->translate('Line 1', 'fr', 'en')->shouldBeCalled()->willReturn('Line 1 translated');
        $this->translationManager->driver('driver2')->willThrow(new \Exception());

        $stackTranslator = new StackTranslator([
            'translators' => ['driver1', 'driver2', 'driver3']
        ], $this->translationManager->reveal());

        $this->assertEquals('Line 1 translated',
            $stackTranslator->translate('Line 1', 'fr', 'en')
        );
    }
}
