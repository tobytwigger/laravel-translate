<?php

namespace Twigger\Tests\Translate\Unit\Translate;

use Illuminate\Contracts\Container\Container;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\TranslationFactory;
use Twigger\Translate\Translate\TranslationInterceptor;
use Twigger\Translate\Translate\Translator;

class TranslationFactoryTest extends TestCase
{

    /** @test */
    public function it_can_create_a_translation_with_interceptors(){
        $container = $this->prophesize(Container::class);
        $container->make(TranslationFactoryTestDummyInterceptor1::class, Argument::any())
            ->will(function($args) {
                return new $args[0]([], $args[1]['translator']);
            });

        $factory = new TranslationFactory($container->reveal());
        $factory->intercept(TranslationFactoryTestDummyInterceptor1::class);

        $translator = $this->prophesize(Translator::class);

        $created = $factory->create($translator->reveal());

        $this->assertInstanceOf(TranslationFactoryTestDummyInterceptor1::class, $created);
    }

    /** @test */
    public function it_throws_an_exception_if_the_interceptor_does_not_extend_translation_interceptor(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The translation interceptor [TestClass] must extend \Twiggeer\Translate\Translate\TranslationInterceptor');

        $container = $this->prophesize(Container::class);
        $factory = new TranslationFactory($container->reveal());
        $factory->intercept('TestClass');
    }
}

class TranslationFactoryTestDummyInterceptor1 extends TranslationInterceptor
{

    protected function canIntercept(string $line, string $to, string $from): bool
    {
        return true;
    }

    protected function get(string $line, string $to, string $from): string
    {
        return 'd';
    }

    protected function save(string $line, string $to, string $from, string $translation): void
    {
    }
}
