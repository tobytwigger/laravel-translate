<?php

namespace Twigger\Translate\Locale;

use Exception;
use Illuminate\Contracts\Foundation\Application;

/**
 * Create the detection chain
 *
 * @see Detector
 */
class DetectorFactory
{

    /**
     * The underlying application instance to resolve detectors from
     *
     * @var Application
     */
    private $application;

    /**
     * Holds the store for retrieving detection strategies
     *
     * @var DetectionStrategyStore
     */
    private $detectionStrategyStore;

    /**
     * @param Application $application
     * @param DetectionStrategyStore $detectionStrategyStore
     */
    public function __construct(Application $application, DetectionStrategyStore $detectionStrategyStore)
    {
        $this->application = $application;
        $this->detectionStrategyStore = $detectionStrategyStore;
    }

    /**
     * Create a detector instance to use to resolve the requested language
     *
     * @return Detector
     * @throws Exception
     */
    public function create(): Detector
    {
        return new Detector(
          $this->getChain()
        );
    }

    /**
     * Get the first detection strategy to use, set up with a chain of successors
     *
     * @return DetectionStrategy A detection strategy with a chain
     *
     * @throws Exception If no locale detection strategies are registered
     */
    private function getChain(): DetectionStrategy
    {
        $strategies = $this->detectionStrategyStore->all();

        if (empty($strategies)) {
            throw new Exception('No locale detection strategies registered');
        }

        for ($i = 0; $i < (count($strategies)); $i++) {
            $strategies[$i] = $this->application->make($strategies[$i]);
        }

        for ($i = 0; $i < (count($strategies) - 1); $i++) {
            $strategies[$i]->setNext($strategies[$i + 1]);
        }
        return $strategies[0];
    }

    /**
     * Allow the factory to be used as a detector
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        return $this->create()->{$name}(...$arguments);
    }

}
