<?php

namespace Omega\Config;

class Combined implements ConfigurationInterface
{
    /**
     * @var ConfigurationInterface[]
     */
    private $_pool = array();

    /**
     * Constructor
     *
     * @param ConfigurationInterface..
     */
    public function __construct()
    {
        if (func_num_args() > 0) {
            foreach (func_get_args() as $arg) {
                $this->add($arg);
            }
        }
    }

    /**
     * Adds new configuration object to Combined pool
     *
     * @param ConfigurationInterface $config
     * @return $this
     */
    public function add(ConfigurationInterface $config)
    {
        $this->_pool[] = $config;
        return $this;
    }

    /**
     * Returns candidate, that may contain path
     *
     * @param string $path
     * @return ConfigurationInterface
     * @throws \LogicException
     */
    protected function _getCandidate($path)
    {
        if (count($this->_pool) == 0) {
            throw new \LogicException(
                'No configs provided'
            );
        }
        $row = null;
        foreach ($this->_pool as $row) {
            if ($row->has($path)) {
                return $row;
            }
        }

        return $row;
    }

    /**
     * Returns true if current configuration has provided path
     *
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        return $this->_getCandidate($path)->has($path);
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
        return $this->_getCandidate($path)->getBool($path);
    }

    /**
     * Returns string
     *
     * @param string $path
     * @return string
     */
    public function getString($path)
    {
        return $this->_getCandidate($path)->getString($path);
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
        return $this->_getCandidate($path)->getInteger($path);
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
        return $this->_getCandidate($path)->getFloat($path);
    }

    /**
     * Returns array
     *
     * @param $path
     * @return array
     */
    public function getArray($path)
    {
        return $this->_getCandidate($path)->getArray($path);
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
        try {
            return $this->_getCandidate($path)->getBool($path);
        } catch (\Exception $e) {
            return $default;
        }
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
        try {
            return $this->_getCandidate($path)->getString($path);
        } catch (\Exception $e) {
            return $default;
        }
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
        try {
            return $this->_getCandidate($path)->getInteger($path);
        } catch (\Exception $e) {
            return $default;
        }
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
        try {
            return $this->_getCandidate($path)->getFloat($path);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Returns flat representations of config
     *
     * @return array
     */
    public function getFlatList()
    {
        $answer = array();
        foreach ($this->_pool as $row) {
            $answer = array_merge($answer, $row->getFlatList());
        }

        return $answer;
    }

}