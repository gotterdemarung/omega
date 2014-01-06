<?php

namespace Omega\DI;


use Symfony\Component\HttpFoundation\Request;

interface HTTPRequestDI {
    /**
     * Sets HTTP Foundation Request
     *
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request);

    /**
     * Returns HTTP Foundation Request
     *
     * @return Request
     */
    public function getRequest();
} 