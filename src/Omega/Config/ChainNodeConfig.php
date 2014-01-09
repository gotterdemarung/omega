<?php

namespace Omega\Config;

use Omega\Type\ChainNode as ChainNodeType;

/**
 * Class Hardcoded
 * Hardcoded implementation of configuration
 *
 * @package Omega\Config
 * @todo tests
 */
class ChainNodeConfig extends ChainNodeType implements ConfigurationInterface
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Returns true if current configuration has provided path
     *
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        return $this->path($path)->isNull();
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
        $this->path($path)->set($value);
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
        return $this->path($path)->isTrue();
    }

    /**
     * Returns string
     *
     * @param string $path
     * @return string
     */
    public function getString($path)
    {
        return $this->path($path)->getString();
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
        return $this->path($path)->getInt();
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
        return $this->path($path)->getFloat();
    }

    /**
     * Returns array
     *
     * @param $path
     * @return array
     */
    public function getArray($path)
    {
        return $this->path($path);
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
        if (!$this->path($path)->isBool()) {
            return $default;
        }

        return $this->path($path)->isTrue();
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
        return $this->path($path)->getString($default);
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
        return $this->path($path)->getInt($default);
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
        return $this->path($path)->getFloat($default);
    }

    /**
     * Returns flat representations of config
     *
     * @return array
     */
    public function getFlatList()
    {
        $answer = $this->flatten();
        if ($answer === null) {
            return array();
        }

        return $answer;
    }


    /**
     * Injects own values into provided config if they are not set
     *
     * @param ConfigurationInterface $defaults
     * @return void
     */
    public function deepInjectDefaults(ConfigurationInterface $defaults)
    {
        foreach ($defaults->getFlatList() as $path => $value) {
            if (!$this->has($path)) {
                $this->set($path, $value);
            }
        }
    }


}