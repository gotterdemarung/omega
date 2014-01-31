<?php

namespace OmegaTest;

use Omega\Application;
use Omega\Config\ChainNodeConfig;

class ApplicationTest extends Test
{

    public function testConfigurator()
    {
        $x = new ApplicationTestTarget(
            new ChainNodeConfig(null)
        );
    }

}

class ApplicationTestTarget extends Application
{
    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        // do nothing
    }

}