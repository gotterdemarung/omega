<?php

namespace OmegaTest\IO;


use Omega\IO\Blackhole;
use OmegaTest\Test;

class BlackholeTest extends Test
{

    public function testWrite()
    {
        $b = new Blackhole();
        $b->write(1);
        $b->write('foo');
        $b->write($b);
        $b->write(null);
        $b->write(false);
        $this->ok();
    }

    public function testWriteln()
    {
        $b = new Blackhole();
        $b->writeln(1);
        $b->writeln('foo');
        $b->writeln($b);
        $b->writeln(null);
        $b->writeln(false);
        $this->ok();
    }
}