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
        $cache->forever(md5(CacheInterceptor::class . 'Testenfr'), 'New Line');

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

        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Testenfr')));


        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals('New Line', $interceptor->translate('Test', 'en', 'fr'));

        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Testenfr')));
    }

    /** @test */
    public function it_can_return_and_save_a_mixture_of_translations_and_cached_translations(){
        $cache = new Repository(new ArrayStore());
        $cache->forever(md5(CacheInterceptor::class . 'Test2enfr'), 'New Line 2');
        $cache->forever(md5(CacheInterceptor::class . 'Test3enfr'), 'New Line 3');
        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Testenfr')));
        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Test4enfr')));

        $translator = $this->prophesize(Translator::class);
        $translator->translateMany(['Test', 'Test4'], 'en', 'fr')->shouldBeCalled()->willReturn(['New Line', 'New Line 4']);

        $interceptor = new CacheInterceptor([], $translator->reveal(), $cache);
        $this->assertEquals([
            'New Line', 'New Line 2', 'New Line 3', 'New Line 4'
        ], $interceptor->translateMany([
            'Test', 'Test2', 'Test3', 'Test4'
            ], 'en', 'fr'));

        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Testenfr')));
        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Test4enfr')));

    }

    /** @test */
    public function it_can_return_all_translations_from_the_cache(){
        $cache = new Repository(new ArrayStore());

        $cache->forever(md5(CacheInterceptor::class . 'Testenfr'), 'New Line');
        $cache->forever(md5(CacheInterceptor::class . 'Test2enfr'), 'New Line 2');
        $cache->forever(md5(CacheInterceptor::class . 'Test3enfr'), 'New Line 3');
        $cache->forever(md5(CacheInterceptor::class . 'Test4enfr'), 'New Line 4');

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

        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Testenfr')));
        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Test2enfr')));
        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Test3enfr')));
        $this->assertFalse($cache->has(md5(CacheInterceptor::class . 'Test4enfr')));

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

        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Testenfr')));
        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Test2enfr')));
        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Test3enfr')));
        $this->assertTrue($cache->has(md5(CacheInterceptor::class . 'Test4enfr')));
    }

}
