<?php

namespace Omega;


use Omega\Config\ConfigurationException;
use Omega\Config\ConfigurationInterface;
use Omega\Config\Defaults;
use Omega\Core\RunnableInterface;

/**
 * Main application abstraction
 *
 * @package Omega
 * @todo tests
 */
abstract class Application implements RunnableInterface
{

    /**
     * @var ConfigurationInterface
     */
    private $_configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->_configuration = clone $configuration;
        Defaults::getInstance()->injectDefaultsInto($this->_configuration);
        $this->setUpEnvironment();
    }

    /**
     * Returns current configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }

    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public abstract  function run();

    /**
     * Sets up default environment
     *
     * @return void
     * @throws ConfigurationException
     */
    public function setUpEnvironment()
    {
        // Setting internal encoding
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding(
                $this->getConfiguration()->getString(
                    ConfigurationInterface::PATH_INTERNAL_ENCODING
                )
            );
        }

        // Setting timezone
        if (
            $this->getConfiguration()->has(
                ConfigurationInterface::PATH_TIMEZONE
            )
        ) {
            $answer = date_default_timezone_set(
                $this->getConfiguration()->getString(
                    ConfigurationInterface::PATH_TIMEZONE
                )
            );

            if ($answer === false) {
                throw new ConfigurationException(
                    ConfigurationInterface::PATH_TIMEZONE,
                    $this->getConfiguration(),
                    'Cannot set timezone'
                );
            }
        }

        // Setting error reporting and exceptions
        error_reporting(
            $this->getConfiguration()->getInteger(
                ConfigurationInterface::PATH_ERROR_REPORTING
            )
        );
        ini_set(
            'display_errors',
            $this->getConfiguration()->getBool(
                ConfigurationInterface::PATH_DISPLAY_ERRORS
            )
        );
        if (
            $this->getConfiguration()->getBool(
                ConfigurationInterface::PATH_CATCH_PHP_ERRORS
            )
        ) {
            set_error_handler(array($this, 'onPhpError'));
        }
    }

    /**
     * Callback to be called on any PHP errors
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @throws \Exception
     */
    public function onPhpError($errno, $errstr, $errfile, $errline)
    {
        throw new \Exception(
            "PHP error in {$errfile} line {$errline} with message {$errstr}",
            $errno
        );
    }

}