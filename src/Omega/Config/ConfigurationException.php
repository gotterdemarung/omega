<?php

namespace Omega\Config;

class ConfigurationException extends \Exception
{

    /**
     * @var string
     */
    private $_key;
    /**
     * @var ConfigurationInterface
     */
    private $_configuration;
    /**
     * @var string
     */
    private $_details;

    public function __construct(
        $key,
        ConfigurationInterface $cnf = null,
        $details = null
    )
    {
        parent::__construct("Configuration key `{$key}` not found or wrong");
        $this->_key = $key;
        $this->_configuration = $cnf;
        $this->_details = $details;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * @return string
     */
    public function getDetailsMessage()
    {
        return $this->_details;
    }



} 