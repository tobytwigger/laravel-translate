<?php

namespace Twigger\Tests\Translate\Unit\Translate\Interceptors;

use Illuminate\Contracts\Cache\Repository as Cache;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\Translator;

class CacheInterceptorTest extends TestCase
{

    /** @test */
    public function it_returns_a_single_translation_from_the_cache_if_possible(){
        $cache = $this->prophesize(Cache::class);
        $cache->has(md5(CacheInterceptor::class . 'Testenfr'))->willReturn(true);
        $cache->get(md5(CacheInterceptor::class . 'Testenfr'), null)->willReturn('New Line');

        $translator = $this->prophesize(Translator::class);
        $translator->translate(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache->reveal());
        $this->assertEquals('New Line', $interceptor->translate('Test', 'en', 'fr'));
    }

    /** @test */
    public function it_saves_a_new_translation_in_the_cache_forever(){
        $cache = $this->prophesize(Cache::class);
        $cache->has(md5(CacheInterceptor::class . 'Testenfr'))->willReturn(false);
        $cache->forever(md5(CacheInterceptor::class . 'Testenfr'), 'New Line')->shouldBeCalled();

        $translator = $this->prophesize(Translator::class);
        $translator->translate('Test', 'en', 'fr')->shouldBeCalled()->willReturn('New Line');

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache->reveal());
        $this->assertEquals('New Line', $interceptor->translate('Test', 'en', 'fr'));
    }

    /** @test */
    public function it_can_return_and_save_a_mixture_of_translations_and_cached_translations(){
        $cache = $this->prophesize(Cache::class);
        $cache->has(md5(CacheInterceptor::class . 'Testenfr'))->willReturn(false);
        $cache->has(md5(CacheInterceptor::class . 'Test2enfr'))->willReturn(true);
        $cache->has(md5(CacheInterceptor::class . 'Test3enfr'))->willReturn(true);
        $cache->has(md5(CacheInterceptor::class . 'Test4enfr'))->willReturn(false);

        $cache->forever(md5(CacheInterceptor::class . 'Testenfr'), 'New Line')->shouldBeCalled();
        $cache->forever(md5(CacheInterceptor::class . 'Test4enfr'), 'New Line 4')->shouldBeCalled();

        $cache->getMultiple([
            md5(CacheInterceptor::class . 'Test2enfr'), md5(CacheInterceptor::class . 'Test3enfr')
        ])->willReturn(['New Line 2', 'New Line 3']);

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test', 'Test4'], 'en', 'fr')->shouldBeCalled()->willReturn(['New Line', 'New Line 4']);

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache->reveal());
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
            ], 'en', 'fr'));
    }

    /** @test */
    public function it_can_return_all_translations_from_the_cache(){
        $cache = $this->prophesize(Cache::class);
        $cache->has(md5(CacheInterceptor::class . 'Testenfr'))->willReturn(true);
        $cache->has(md5(CacheInterceptor::class . 'Test2enfr'))->willReturn(true);
        $cache->has(md5(CacheInterceptor::class . 'Test3enfr'))->willReturn(true);
        $cache->has(md5(CacheInterceptor::class . 'Test4enfr'))->willReturn(true);

        $cache->getMultiple([
            md5(CacheInterceptor::class . 'Testenfr'),
            md5(CacheInterceptor::class . 'Test2enfr'),
            md5(CacheInterceptor::class . 'Test3enfr'),
            md5(CacheInterceptor::class . 'Test4enfr')
        ])->willReturn([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ]);

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache->reveal());
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
        ], 'en', 'fr'));
    }

    /** @test */
    public function it_can_return_and_save_all_translations(){
        $cache = $this->prophesize(Cache::class);
        $cache->has(md5(CacheInterceptor::class . 'Testenfr'))->willReturn(false);
        $cache->has(md5(CacheInterceptor::class . 'Test2enfr'))->willReturn(false);
        $cache->has(md5(CacheInterceptor::class . 'Test3enfr'))->willReturn(false);
        $cache->has(md5(CacheInterceptor::class . 'Test4enfr'))->willReturn(false);

        $cache->forever(md5(CacheInterceptor::class . 'Testenfr'), 'New Line')->shouldBeCalled();
        $cache->forever(md5(CacheInterceptor::class . 'Test2enfr'), 'New Line 2')->shouldBeCalled();
        $cache->forever(md5(CacheInterceptor::class . 'Test3enfr'), 'New Line 3')->shouldBeCalled();
        $cache->forever(md5(CacheInterceptor::class . 'Test4enfr'), 'New Line 4')->shouldBeCalled();

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test', 'Test2', 'Test3', 'Test4'], 'en', 'fr')
            ->shouldBeCalled()
            ->willReturn(['New Line', 'New Line 2', 'New Line 3', 'New Line 4']);

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache->reveal());
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
        ], 'en', 'fr'));
    }

}
