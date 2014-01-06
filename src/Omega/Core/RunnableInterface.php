<?php

namespace Omega\Core;


interface RunnableInterface
{
    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run();
}