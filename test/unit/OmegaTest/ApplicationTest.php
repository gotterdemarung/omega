<?php

namespace OmegaTest;

use Omega\Application;

class ApplicationTest extends Test
{

    public function testConstructor()
    {
        $this->fail('todo');
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