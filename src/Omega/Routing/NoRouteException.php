<?php

namespace Omega\Routing;

use Symfony\Component\HttpFoundation\Request;

class NoRouteException extends \Exception
{
    public function __construct(Request $request)
    {
        parent::__construct(
            'No route found for'
            . ' [' . $request->getRealMethod() . ']'
            . ' ' . $request->getUri()
        );
    }
}