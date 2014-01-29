<?php

namespace Omega;

use Omega\Config\ConfigurationInterface;
use Omega\DI\HTTPRequestDI;
use Omega\Events\StringDebugEvent;
use Omega\Events\StringKeyIncrementEvent;
use Omega\Routing\CommonRouter;
use Omega\Routing\RouteInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebApplication
 *
 * @package Omega
 * @todo tests
 */
class WebApplication extends Application
{
    /**
     * @var Request
     */
    private $_request;

    public function __construct(ConfigurationInterface $config)
    {
        parent::__construct($config);
        $this->setRequest(Request::createFromGlobals());

        $this->sendEvent(
            new StringDebugEvent($this, 'Web application constructed')
        );
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
     * {@inheritdoc}
     */
    public function setUpServiceLocator()
    {
        parent::setUpServiceLocator();

        // Binding router
        $serviceKey = 'Omega\Routing\RouterInterface';
        if (!$this->getServiceLocator()->getService($serviceKey)) {
            $this->getServiceLocator()->registerService(
                $serviceKey,
                new CommonRouter()
            );
        }
    }


    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $this->sendEvent(
            new StringKeyIncrementEvent($this, 'AppStart')
        );

        /** @var RouteInterface $router */
        $router = $this->getServiceLocator()->getService(
            'Omega\Routing\RouterInterface'
        );
        $router->process($this->getRequest());
    }

} 