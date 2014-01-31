<?php

namespace Omega;
use Omega\Config\ConfigurationInterface;
use Omega\DI\ServiceLocatorInterface;
use Symfony\Component\Console\Application as SymfonyConsoleApp;
use Symfony\Component\Console\Command\Command;

/**
 * Class ConsoleApplication
 * Wrapper for Symfony console application
 *
 * @package Omega
 */
class ConsoleApplication extends Application
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    protected $_symfonyApp;

    /**
     * @param ConfigurationInterface  $config  Configuration instance
     * @param ServiceLocatorInterface $sli     Service locator instance
     * @param string|null             $name    Name of console application
     * @param string                  $version Version of console application
     */
    public function __construct(
        ConfigurationInterface $config,
        ServiceLocatorInterface $sli = null,
        $name = null,
        $version = '1.0'
    )
    {
        parent::__construct($config, $sli);
        $this->_symfonyApp = new SymfonyConsoleApp($name, $version);
    }

    /**
     * Registers new console command
     *
     * @param Command $cmd
     */
    public function addCommand(Command $cmd)
    {
        $this->_symfonyApp->add($cmd);
    }


    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $this->_symfonyApp->run();
    }

} 