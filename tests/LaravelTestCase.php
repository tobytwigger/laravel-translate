<?php

namespace Twigger\Tests\Translate;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Prophecy\PhpUnit\ProphecyTrait;
use Twigger\Translate\TranslationServiceProvider;

class LaravelTestCase extends \Orchestra\Testbench\TestCase
{
    use ProphecyTrait;

    protected function getPackageProviders($app)
    {
        return [TranslationServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }


}
