<?php

namespace Twigger\Translate\Translate;

use Twigger\Translate\Translate\Handlers\AWSTranslator;
use Twigger\Translate\Translate\Handlers\Cache;
use Twigger\Translate\Translate\Handlers\Chain;
use Twigger\Translate\Translate\Handlers\Database;
use Closure;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @method static string translate(string $line, string $lang) Translate a single line
 * @method static string translateMany(array $line, string $lang) Translate an array of lines
 */
class TranslationManager
{

    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    protected $configuration = [];

    protected $defaultDriver;
    /**
     * Create a new Translation manager instance.
     *
     * @param  Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get a translation driver instance.
     *
     * @param  string|null  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        return $this->resolve($driver ?? $this->getDefaultDriver());
    }

    /**
     * Resolve the given translation instance by name.
     *
     * @param  string  $name
     * @return Translator
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->configurationFor($name);

        if(!array_key_exists('driver', $config) && $config['driver']) {
            throw new \Exception(sprintf('Translation configuration does not supply a driver'));
        }
        if (isset($this->customCreators[$config['driver']])) {
            return $this->container->make(TranslationFactory::class)
                ->create($this->callCustomCreator($config));
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->container, $config);
    }

    /**
     * Get the translation connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function configurationFor($name)
    {
        if(array_key_exists($name, $this->configuration)) {
            return $this->configuration[$name];
        }

        throw new InvalidArgumentException("Translator [{$name}] is not defined.");

    }

    /**
     * Get the default translation driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultDriver;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function pushDriver(string $driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    public function pushConfiguration(string $name, array $configuration)
    {
        $this->configuration[$name] = $configuration;
    }

    public function setDefaultDriver(string $driver)
    {
        $this->defaultDriver = $driver;
    }

}
