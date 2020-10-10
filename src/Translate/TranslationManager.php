<?php

namespace Twigger\Translate\Translate;

use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Handles creating translation services
 *
 * @method static string translate(string $line, string $to, string $from) Translate a single line
 * @method static string translateMany(array $line, string $to, string $from) Translate an array of lines
 *
 * @see Translator
 */
class TranslationManager
{

    /**
     * Define the key in the configuration that will represent the driver
     */
    const DRIVER_KEY = '__translator_driver_registration_name';

    /**
     * The container instance to pass to a driver callback function
     *
     * @var Container
     */
    protected $container;

    /**
     * The registered driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The registered configurations.
     *
     * The key is the name of the configuration, and the value is an array of configuration values.
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * Holds the default configuration to use
     *
     * @var string
     */
    protected $defaultDriver;

    /**
     * Holds the translation factory to create a translation
     *
     * @var TranslationFactory
     */
    private $translationFactory;

    /**
     * Create a new Translation manager instance.
     *
     * @param Container $container
     * @param TranslationFactory $translationFactory Used for setting up a translation
     */
    public function __construct(Container $container, TranslationFactory $translationFactory)
    {
        $this->container = $container;
        $this->translationFactory = $translationFactory;
    }

    /**
     * Get a translation driver instance.
     *
     * @param string|null $driver The name of the configuration to use
     * @return Translator
     *
     * @throws \Exception
     */
    public function driver($driver = null): Translator
    {
        return $this->resolve($driver ?? $this->getDefaultDriver());
    }

    /**
     * Resolve the given translation instance by name.
     *
     * @param string $name The name of the configuration to use
     * @return Translator
     *
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    protected function resolve(string $name): Translator
    {
        $config = $this->configurationFor($name);

        if(!array_key_exists(static::DRIVER_KEY, $config) || !$config[static::DRIVER_KEY]) {
            throw new \Exception(sprintf('Translation configuration does not supply a driver'));
        }
        if (isset($this->customCreators[$config[static::DRIVER_KEY]])) {
            return $this->translationFactory->create($this->callCustomCreator($config));
        }

        throw new InvalidArgumentException("Driver [{$config[static::DRIVER_KEY]}] is not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config The configuration to create the driver from
     * @return Translator
     */
    protected function callCustomCreator(array $config): Translator
    {
        return $this->customCreators[$config[static::DRIVER_KEY]]($this->container, $config);
    }

    /**
     * Get the requested configuration
     *
     * @param  string  $name
     * @return array
     */
    protected function configurationFor(string $name): array
    {
        if(array_key_exists($name, $this->configuration)) {
            return $this->configuration[$name];
        }

        throw new InvalidArgumentException("Translator [{$name}] is not defined.");

    }

    /**
     * Get the default translation driver name.
     *
     * @return string The default translation configuration name
     * @throws \Exception If no default translator set
     */
    public function getDefaultDriver(): string
    {
        if(!$this->defaultDriver) {
            throw new \Exception('No default translator has been set');
        }
        return $this->defaultDriver;
    }

    /**
     * Register a new driver.
     *
     * @param  string  $driver The name of the driver
     * @param  \Closure  $callback Function taking the container and the config in that order
     */
    public function pushDriver(string $driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);
    }

    /**
     * Register a new configuration set
     *
     * @param string $name The name of the configuration
     * @param string $driver The name of the driver to use
     * @param array $configuration An array of configuration for the driver
     */
    public function pushConfiguration(string $name, string $driver, array $configuration)
    {
        $configuration[static::DRIVER_KEY] = $driver;
        $this->configuration[$name] = $configuration;
    }

    /**
     * Set the default configuration name
     *
     * @param string $name Name of the configuration to use by default
     */
    public function setDefaultDriver(string $name)
    {
        $this->defaultDriver = $name;
    }


    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
