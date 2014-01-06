<?php

namespace Omega\Config;

/**
 * Class Hardcoded
 * Hardcoded implementation of configuration
 *
 * @package Omega\Config
 * @todo tests
 */
class Hardcoded implements ConfigurationInterface
{
    /**
     * Data container
     *
     * @var mixed[]
     */
    private $_container = array();

    /**
     * Returns true if current configuration has provided path
     *
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        return isset($this->_container[$path]);
    }


    /**
     * Sets configuration value
     *
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function set($path, $value)
    {
        $this->_container[$path] = $value;
    }

    /**
     * Returns boolean
     *
     * @param string $path
     * @return bool
     * @throws ConfigurationException
     */
    public function getBool($path)
    {
        // TODO: Implement getBool() method.
    }

    /**
     * Returns string
     *
     * @param string $path
     * @return string
     */
    public function getString($path)
    {
        // TODO: Implement getString() method.
    }

    /**
     * Returns integer
     *
     * @param string $path
     * @return int
     * @throws ConfigurationException
     */
    public function getInteger($path)
    {
        // TODO: Implement getInteger() method.
    }

    /**
     * Returns float
     *
     * @param string $path
     * @return float
     * @throws ConfigurationException
     */
    public function getFloat($path)
    {
        // TODO: Implement getFloat() method.
    }

    /**
     * Returns boolean, and if not set - $default
     *
     * @param string $path
     * @param bool $default
     * @return bool
     */
    public function getBoolSafe($path, $default)
    {
        // TODO: Implement getBoolSafe() method.
    }

    /**
     * Returns string, and if not set - $default
     *
     * @param string $path
     * @param string $default
     * @return string
     */
    public function getStringSafe($path, $default)
    {
        // TODO: Implement getStringSafe() method.
    }

    /**
     * Returns integer, and if not set - $default
     *
     * @param string $path
     * @param int $default
     * @return int
     */
    public function getIntegerSafe($path, $default)
    {
        // TODO: Implement getIntegerSafe() method.
    }

    /**
     * Returns float, and if not set - $default
     *
     * @param string $path
     * @param float $default
     * @return float
     */
    public function getFloatSafe($path, $default)
    {
        // TODO: Implement getFloatSafe() method.
    }

    /**
     * Injects own values into provided config if they are not set
     *
     * @param ConfigurationInterface $config
     * @return void
     */
    public function injectDefaultsInto(ConfigurationInterface $config)
    {
        foreach ($this->_container as $path => $value) {
            if (!$config->has($path)) {
                $config->set($path, $value);
            }
        }
    }


}