<?php

namespace Twigger\Tests\Translate\Integration\Translate\Interceptors;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\Translator;

class CacheInterceptorTest extends TestCase
{

    /** @test */
    public function it_returns_a_single_translation_from_the_cache_if_possible(){
        $cache = new Repository(new ArrayStore());
        $cache->forever(CacheInterceptor::getCacheKey('Test', 'en', 'fr'), 'New Line');

        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals('New Line', $interceptor->translate('Test', 'en', 'fr'));
    }

    /** @test */
    public function it_saves_a_new_translation_in_the_cache_forever(){
        $cache = new Repository(new ArrayStore());

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test', 'en', 'fr')->shouldBeCalled()->willReturn('New Line');

        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test', 'en', 'fr' )));


        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals('New Line', $interceptor->translate('Test', 'en', 'fr'));

        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test', 'en', 'fr')));
    }

    /** @test */
    public function it_can_return_and_save_a_mixture_of_translations_and_cached_translations(){
        $cache = new Repository(new ArrayStore());
        $cache->forever(CacheInterceptor::getCacheKey('Test2', 'fr', 'en'), 'New Line 2');
        $cache->forever(CacheInterceptor::getCacheKey('Test3', 'fr', 'en'), 'New Line 3');
        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test', 'fr', 'en')));
        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test4', 'fr', 'en')));

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test', 'Test4'], 'fr', 'en')->shouldBeCalled()->willReturn(['New Line', 'New Line 4']);

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
            ], 'fr', 'en'));

        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test', 'fr', 'en')));
        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test4', 'fr', 'en')));

    }

    /** @test */
    public function it_can_return_all_translations_from_the_cache(){
        $cache = new Repository(new ArrayStore());

        $cache->forever(CacheInterceptor::getCacheKey('Test', 'en', 'fr'), 'New Line');
        $cache->forever(CacheInterceptor::getCacheKey('Test2', 'en', 'fr'), 'New Line 2');
        $cache->forever(CacheInterceptor::getCacheKey('Test3', 'en', 'fr'), 'New Line 3');
        $cache->forever(CacheInterceptor::getCacheKey('Test4', 'en', 'fr'), 'New Line 4');

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
        ], 'en', 'fr'));
    }

    /** @test */
    public function it_can_return_and_save_all_translations(){
        $cache = new Repository(new ArrayStore());

        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test', 'en', 'fr')));
        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test2', 'en', 'fr')));
        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test3', 'en', 'fr')));
        $this->assertFalse($cache->has(CacheInterceptor::getCacheKey('Test4', 'en', 'fr')));

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test', 'Test2', 'Test3', 'Test4'], 'en', 'fr')
            ->shouldBeCalled()
            ->willReturn(['New Line', 'New Line 2', 'New Line 3', 'New Line 4']);

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
        ], 'en', 'fr'));

        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test', 'en', 'fr')));
        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test2', 'en', 'fr')));
        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test3', 'en', 'fr')));
        $this->assertTrue($cache->has(CacheInterceptor::getCacheKey('Test4', 'en', 'fr')));
    }

}
