<?php

namespace Omega\Routing;

use Omega\Core\RunnableInterface;
use Symfony\Component\HttpFoundation\Request;

interface RouteInterface extends RunnableInterface
{
    /**
     * Returns true if current route can satisfy provided request
     *
     * @param Request $request
     * @return bool
     */
    public function isSatisfied(Request $request);
}
