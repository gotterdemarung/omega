<?php

namespace Omega\Routing;

interface RouterInterface extends RouteInterface, \Countable
{
    /**
     * Adds route to routes pool
     *
     * @param RouteInterface $route
     * @return void
     */
    public function addRoute(RouteInterface $route);
}