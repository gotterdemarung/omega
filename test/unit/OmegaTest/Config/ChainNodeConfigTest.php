<?php

namespace OmegaTest\Config;

use Omega\Config\ChainNodeConfig;
use OmegaTest\Test;

class ChainNodeConfigTest extends Test {

    public function testLinear()
    {
        $linear = new ChainNodeConfig(array(
            'one' => 1,
            'two' => '2',
            'three' => 3.01,
            'four' => true
        ));

        $this->assertTrue($linear->has('one'));
        $this->assertTrue($linear->has('two'));
        $this->assertTrue($linear->has('three'));
        $this->assertTrue($linear->has('four'));
        $this->assertFalse($linear->has('five'));


        $this->assertSame(1, $linear->getInteger('one'));
        $this->assertSame(2, $linear->getInteger('two'));
        $this->assertSame('2', $linear->getString('two'));
    }

} 