<?php

namespace Omega\Routing;


use Omega\DI\HTTPRequestDI;
use Symfony\Component\HttpFoundation\Request;

class CommonRouter implements RouterInterface, HTTPRequestDI
{
    /**
     * List of registered routes
     *
     * @var RouteInterface[]
     */
    private $_routes = array();

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
     * Returns true if current route can satisfy provided request
     *
     * @param Request $request
     * @return bool
     */
    public function isSatisfied(Request $request)
    {
        foreach ($this->_routes as $route) {
            if ($route->isSatisfied($request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Runs execution
     *
     * @param Request $request
     * @throws NoRouteException
     * @return void
     */
    public function process(Request $request)
    {
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
                $route->process($request);
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