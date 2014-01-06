<?php

namespace Omega\Routing;


use Omega\DI\HTTPRequestDI;
use Symfony\Component\HttpFoundation\Request;

class CommonRouter implements RouterInterface, HTTPRequestDI
{
    /**
     * List of registered routes
     *
     * @var RouterInterface[]
     */
    private $_routes = array();

    /**
     * @var Request
     */
    private $_request = null;

    /**
     * Sets Http Foundation Request for router
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Returns Http Foundation Request for router
     * If not setted before - creates new, based on globals, but dont
     * cache it
     *
     * @return Request
     */
    public function getRequest()
    {
        if ($this->_request == null) {
            return Request::createFromGlobals();
        }
        return $this->_request;
    }

    /**
     * Adds route to routes pool
     *
     * @param RouteInterface $route
     * @return void
     */
    public function addRoute(RouteInterface $route)
    {
        $this->_routes[] = $route;
    }

    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $request = $this->getRequest();

        if ($this->count() == 0) {
            // No registered routes
            throw new NoRouteException($request);
        }

        $routeFound = false;
        foreach ($this->_routes as $route) {
            /** @var RouteInterface $route */
            if (!$route->isSatisfied($request)) {
                continue;
            }

            try {
                if ($route instanceof HTTPRequestDI) {
                    /** @var HTTPRequestDI $route */
                    $route->setRequest($request);
                }
                $route->run();
                $routeFound = true;
                break;
            } catch (NoRouteException $nre) {
                // Do nothing
            }
        }

        if (!$routeFound) {
            // No route found
            throw new NoRouteException($request);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->_routes);
    }
}