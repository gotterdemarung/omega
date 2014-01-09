<?php

namespace Omega\DI\ServiceLocators;


use Omega\DI\ServiceLocatorInterface;

class Common implements ServiceLocatorInterface
{
    /**
     * List of service implementations
     * @var array
     */
    private $_implementations = array();

    /**
     * Registers service implementation
     *
     * @param string $serviceName Name of service
     * @param string|object $classOrObject If object provided, works as singleton, creates instance otherwise
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function registerService($serviceName, $classOrObject)
    {
        if ($serviceName === null) {
            throw new \InvalidArgumentException(
                'Service name not provided'
            );
        }
        if ($classOrObject === null) {
            throw new \InvalidArgumentException(
                'implementation not provided'
            );
        }

        $serviceName = trim(strtolower($serviceName));

        $this->_implementations[$serviceName] = $classOrObject;
    }

    /**
     * Returns true if locator contains information about requested service
     *
     * @param $serviceName
     * @return bool
     */
    public function hasService($serviceName)
    {
        $serviceName = trim(strtolower($serviceName));

        return isset($this->_implementations[$serviceName]);
    }

    /**
     * Returns service implementation
     *
     * @param string $serviceName
     * @param null|array $arguments
     * @return mixed
     */
    public function getService($serviceName, $arguments = null)
    {
        $serviceName = trim(strtolower($serviceName));

        if (!$this->hasService($serviceName)) {
            return null;
        }

        $service = $this->_implementations[$serviceName];
        if (!is_string($service)) {
            // Object -- using as singleton
            return $service;
        }

        // service implementation is string
        // thus - instantiating it
        $ref = new \ReflectionClass($service);
        if ($arguments === null || count($arguments) == 0) {
            return $ref->newInstance();
        } else {
            return $ref->newInstanceArgs($arguments);
        }
    }


} 