<?php

namespace Twigger\Tests\Translate\Unit\Translate;

use Illuminate\Contracts\Container\Container;
use Prophecy\Argument;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\TranslationFactory;
use Twigger\Translate\Translate\TranslationManager;
use Twigger\Translate\Translate\Translator;

class TranslationManagerTest extends TestCase
{

    /** @test */
    public function the_manager_resolves_the_correct_driver_with_the_correct_configuration(){

        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushDriver('test-driver', function($container, $config) {
            return new TestDriver($config);
        });

        $manager->pushConfiguration('test-config', 'test-driver', ['test' => 'config-value']);
        $driver = $manager->driver('test-config');

        $this->assertInstanceOf(TestDriver::class, $driver);
        $this->assertEquals('config-value', $driver->getConfigFromKey('test'));
    }

    /** @test */
    public function the_default_configuration_is_used(){
        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushDriver('test-driver', function($container, $config) {
            return new TestDriver($config);
        });

        $manager->pushConfiguration('test-config', 'test-driver', ['test' => 'config-value']);
        $manager->pushConfiguration('test-config-2', 'test-driver', ['test' => 'config-value-2']);

        $manager->setDefaultDriver('test-config-2');

        $driver = $manager->driver(null);

        $this->assertInstanceOf(TestDriver::class, $driver);
        $this->assertEquals('config-value-2', $driver->getConfigFromKey('test'));
    }

    /** @test */
    public function it_throws_an_exception_if_no_default_driver_registered_but_default_needed(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No default translator has been set');

        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushDriver('test-driver', function($container, $config) {
            return new TestDriver($config);
        });

        $manager->pushConfiguration('test-config', 'test-driver', ['test' => 'config-value']);
        $manager->pushConfiguration('test-config-2', 'test-driver', ['test' => 'config-value-2']);

        $driver = $manager->driver(null);

    }

    /** @test */
    public function it_throws_an_exception_if_the_config_has_no_driver(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Translation configuration does not supply a driver');

        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushConfiguration('test-config', '', ['test' => 'config-value']);

        $driver = $manager->driver('test-config');
    }

    /** @test */
    public function it_throws_an_exception_if_the_config_driver_is_not_registered(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Driver [test-driver] is not supported.');

        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushConfiguration('test-config', 'test-driver', ['test' => 'config-value']);

        $driver = $manager->driver('test-config');
    }

    /** @test */
    public function it_throws_an_exception_if_the_configuration_is_not_recognised(){

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Translator [test-config] is not defined');

        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushDriver('test-driver', function($container, $config) {
            return new TestDriver($config);
        });

        $driver = $manager->driver('test-config');
    }

    /** @test */
    public function it_can_act_as_a_proxy_for_the_default_configuration(){
        $container = $this->prophesize(Container::class);
        $translationFactory = $this->prophesize(TranslationFactory::class);
        $translationFactory->create(Argument::any())->will(function($args) {
            return $args[0];
        });

        $manager = new TranslationManager($container->reveal(), $translationFactory->reveal());

        $manager->pushDriver('test-driver', function($container, $config) {
            return new TestDriver($config);
        });

        $manager->pushConfiguration('test-config', 'test-driver', ['test' => 'config-value']);

        $manager->setDefaultDriver('test-config');

        $this->assertEquals('Translated by Test Driver to fr from en', $manager->translate('Test', 'fr', 'en'));
    }

}

class TestDriver extends Translator {

    public function translate(string $line, string $to, string $from): ?string
    {
        return 'Translated by Test Driver to ' . $to . ' from ' . $from;
    }

    public function getConfigFromKey(string $key)
    {
        return $this->getConfig($key);
    }
}
