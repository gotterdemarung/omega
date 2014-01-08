<?php

namespace Omega\Model;

interface IdentifiedInterface
{
    /**
     * Returns ID of current object
     *
     * @return mixed
     */
    public function getId();
}