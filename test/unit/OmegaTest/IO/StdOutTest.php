<?php

namespace OmegaTest\IO;

use Omega\IO\StdOut;
use OmegaTest\Test;

class StdOutTest extends Test
{
    public function testWrite()
    {
        ob_start();

        $b = new StdOut();
        $b->write(432);
        $b->write('foo');
        $b->write(null);
        $b->write(false);
        $b->write(true);
        $b->write(2.99);

        $this->assertSame('432foo12.99', ob_get_clean());
    }

    public function testWriteln()
    {
        ob_start();

        $b = new StdOut();
        $b->writeln(432);
        $b->writeln('foo');
        $b->writeln(null);
        $b->writeln(false);
        $b->writeln(true);
        $b->writeln(2.99);

        $this->assertSame("432\nfoo\n\n\n1\n2.99\n", ob_get_clean());
    }

}