<?php

namespace Omega\Routing;


use Omega\Core\RunnableInterface;

interface RouterInterface extends RunnableInterface, \Countable
{
    /**
     * Adds route to routes pool
     *
     * @param RouteInterface $route
     * @return void
     */
    public function addRoute(RouteInterface $route);
}