<?php

namespace Omega\Config;

use Omega\Type\ChainNode;

/**
 * Class Hardcoded
 * Hardcoded implementation of configuration
 *
 * @package Omega\Config
 */
class ChainNodeConfig implements ConfigurationInterface
{
    /**
     * @var ChainNode
     */
    private $_node;

    public function __construct($data)
    {
        $this->_node = new ChainNode($data);
    }

    /**
     * Returns true if current configuration has provided path
     *
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        return !$this->_node->path($path)->isNull();
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
        $this->_node->path($path)->set($value);
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
        return $this->_node->path($path)->isTrue();
    }

    /**
     * Returns string
     *
     * @param string $path
     * @return string
     */
    public function getString($path)
    {
        return $this->_node->path($path)->getString();
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
        return $this->_node->path($path)->getInt();
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
        return $this->_node->path($path)->getFloat();
    }

    /**
     * Returns array
     *
     * @param $path
     * @return array
     */
    public function getArray($path)
    {
        return $this->_node->path($path);
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
        if (!$this->_node->path($path)->isBool()) {
            return $default;
        }

        return $this->_node->path($path)->isTrue();
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
        return $this->_node->path($path)->getString($default);
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
        return $this->_node->path($path)->getInt($default);
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
        return $this->_node->path($path)->getFloat($default);
    }

    /**
     * Returns flat representations of config
     *
     * @return array
     */
    public function getFlatList()
    {
        $answer = $this->_node->flatten();
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