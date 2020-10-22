<?php

namespace Twigger\Translate\Translate;

use Illuminate\Contracts\Container\Container;

/**
 * Set up a translation with the interceptors
 */
class TranslationFactory
{

    /**
     * Holds the container to retrieve the interceptor classes from
     *
     * @var Container
     */
    private $container;

    /**
     * Holds an array of interceptor classes
     *
     * @var array
     */
    private $interceptor = [];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register an interceptor class
     *
     * @param string $interceptor The class name of the interceptor
     * @throws \Exception If the interceptor is not valid
     */
    public function intercept(string $interceptor)
    {
        if(!is_subclass_of($interceptor, TranslationInterceptor::class)) {
            throw new \Exception(
                sprintf('The translation interceptor [%s] must extend \Twiggeer\Translate\Translate\TranslationInterceptor', $interceptor)
            );
        }
        $this->interceptor[] = $interceptor;
    }

    /**
     * Wrap a translator in the interceptors ready for use
     *
     * @param Translator $translator The base translator service
     *
     * @return Translator A translator instance wrapped in the interceptors
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Translator $translator)
    {
        foreach (array_reverse($this->interceptor) as $interceptor) {
            $translator = $this->container->make($interceptor, ['config' => [], 'translator' => $translator]);
        }
        return $translator;
    }

}
