<?php

namespace Omega;


use Omega\Config\ConfigurationException;
use Omega\Config\ConfigurationInterface;
use Omega\Config\Defaults;
use Omega\Core\RunnableInterface;
use Omega\DI\ServiceLocatorInterface;
use Omega\DI\ServiceLocators\Common as CommonServiceLocator;
use Omega\Events\ChannelInterface;
use Omega\Events\StringDebugEvent;
use Omega\Events\EventInterface;
use Omega\Events\StringKeyIncrementEvent;

/**
 * Main application abstraction
 *
 * @package Omega
 * @todo tests
 */
abstract class Application implements RunnableInterface, ChannelInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $_configuration;

    /**
     * @var ServiceLocatorInterface
     */
    private $_serviceLocator;

    public function __construct(
        ConfigurationInterface $configuration,
        ServiceLocatorInterface $sli = null
    )
    {
        $this->_configuration = clone $configuration;
        if ($sli === null) {
            $this->_serviceLocator = new CommonServiceLocator();
        } else {
            $this->_serviceLocator = $sli;
        }
        $this->_configuration->deepInjectDefaults(Defaults::getInstance());
        $this->setUpServiceLocator();
        $this->setUpEventsChannel();
        $this->setUpEnvironment();

        $this->sendEvent(
            new StringDebugEvent($this, 'Application constructed')
        );
        $this->sendEvent(
            new StringKeyIncrementEvent($this, 'AppConstruct')
        );
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
     * Returns application's service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public abstract  function run();

    /**
     * Sets up events channel
     *
     * @return void
     */
    public function setUpEventsChannel()
    {
        $serviceKey = 'Omega\Events\ChannelInterface';

        if ($this->getServiceLocator()->hasService($serviceKey)) {
            // Already set
            return;
        }

        // TODO
    }

    /**
     * Sets up default service location implementations
     *
     * @return void
     */
    public function setUpServiceLocator()
    {
        // Reading configuration values
        if (
            $this->getConfiguration()->has(
                ConfigurationInterface::PATH_APP_IMPLEMENTATIONS
            )
        ) {
            // Registering configured injections
            foreach (
                $this->getConfiguration()->getArray(
                    ConfigurationInterface::PATH_APP_IMPLEMENTATIONS
                )
                as $serviceName => $serviceImplementation
            ) {
                $this->getServiceLocator()->registerService(
                    $serviceName,
                    $serviceImplementation
                );
            }
        }
    }

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

    /**
     * {@inheritdoc}
     */
    public function sendEvent(EventInterface $event)
    {
        $serviceKey = 'Omega\Events\ChannelInterface';

        if ($this->getServiceLocator()->hasService($serviceKey)) {
            $this->getServiceLocator()
                ->getService($serviceKey)
                ->sendEvent($event);
        }
    }


}