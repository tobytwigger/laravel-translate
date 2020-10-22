<?php

namespace Twigger\Tests\Translate\Unit\Locale;

use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Locale\DetectionStrategyStore;

class DetectionStrategyStoreTest extends TestCase
{

    /** @test */
    public function it_returns_all_registered_strategies(){
        $store = new DetectionStrategyStore();

        $store->register('TestClass1');
        $store->register('TestClass2');
        $store->register('TestClass3');

        $this->assertEquals([
            'TestClass1',
            'TestClass2',
            'TestClass3'
        ], $store->all());
    }

    /** @test */
    public function a_strategy_can_be_prepended(){
        $store = new DetectionStrategyStore();

        $store->register('TestClass1');
        $store->register('TestClass2');
        $store->register('TestClass3');

        $store->registerFirst('TestClass4');
        $store->registerFirst('TestClass5');

        $this->assertEquals([
            'TestClass4',
            'TestClass5',
            'TestClass1',
            'TestClass2',
            'TestClass3'
        ], $store->all());
    }

    /** @test */
    public function a_strategy_can_be_appended(){
        $store = new DetectionStrategyStore();

        $store->register('TestClass1');
        $store->register('TestClass2');
        $store->register('TestClass3');

        $store->registerLast('TestClass4');
        $store->registerLast('TestClass5');

        $this->assertEquals([
            'TestClass1',
            'TestClass2',
            'TestClass3',
            'TestClass4',
            'TestClass5'
        ], $store->all());
    }

    /** @test */
    public function strategies_can_be_both_appended_and_prepended(){
        $store = new DetectionStrategyStore();

        $store->register('TestClass1');
        $store->register('TestClass2');
        $store->register('TestClass3');


        $store->registerFirst('TestClass4');
        $store->registerFirst('TestClass5');

        $store->registerLast('TestClass6');
        $store->registerLast('TestClass7');

        $this->assertEquals([
            'TestClass4',
            'TestClass5',
            'TestClass1',
            'TestClass2',
            'TestClass3',
            'TestClass6',
            'TestClass7'
        ], $store->all());
    }



}
