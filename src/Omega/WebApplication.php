<?php

namespace Omega;

use Omega\Config\ConfigurationInterface;
use Omega\DI\HTTPRequestDI;
use Omega\Routing\CommonRouter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebApplication
 *
 * @package Omega
 * @todo tests
 */
class WebApplication extends Application implements HTTPRequestDI
{
    /**
     * @var Request
     */
    private $_request;

    public function __construct(ConfigurationInterface $config)
    {
        parent::__construct($config);
        $this->setRequest(Request::createFromGlobals());
    }

    /**
     * Sets HTTP Foundation Request
     *
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Returns HTTP Foundation Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $router = new CommonRouter();
        $router->setRequest($this->getRequest());
        $router->run();
    }

} 