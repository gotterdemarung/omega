<?php

namespace Omega\DI;


interface ServiceLocatorInterface
{
    /**
     * Registers service implementation
     *
     * @param string        $serviceName   Name of service
     * @param string|object $classOrObject If object provided, works as singleton, creates instance otherwise
     * @return mixed
     */
    public function registerService($serviceName, $classOrObject);

    /**
     * Returns true if locator contains information about requested service
     *
     * @param $serviceName
     * @return bool
     */
    public function hasService($serviceName);

    /**
     * Returns service implementation
     *
     * @param string     $serviceName
     * @param null|array $arguments
     * @return mixed
     */
    public function getService($serviceName, $arguments = null);
} 