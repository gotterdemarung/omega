<?php

namespace Omega\Events;


interface EventInterface
{
    /**
     * Returns time of event
     *
     * @return float
     */
    public function getTime();

    /**
     * Returns sender object
     *
     * @return object
     */
    public function getSender();
} 