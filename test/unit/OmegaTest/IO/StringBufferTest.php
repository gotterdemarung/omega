<?php

namespace OmegaTest\IO;


use Omega\IO\StringBuffer;
use OmegaTest\Test;

class StringBufferTest extends Test
{
    public function testWrite()
    {
        $b = new StringBuffer();
        $b->write(432);
        $b->write('foo');
        $b->write(null);
        $b->write(false);
        $b->write(true);
        $b->write(2.99);
        $b->write($b);

        $this->assertSame('432foo12.99432foo12.99', $b->getString());
        $this->assertSame('432foo12.99432foo12.99', (string) $b);
        $this->assertSame('432foo12.99432foo12.99', $b->__toString());
    }

    public function testWriteln()
    {
        $b = new StringBuffer();
        $b->writeln(432);
        $b->writeln('foo');
        $b->writeln(null);
        $b->writeln(false);
        $b->writeln(true);
        $b->writeln(2.99);
        $b->writeln($b);

        $this->assertSame("432\nfoo\n\n\n1\n2.99\n432\nfoo\n\n\n1\n2.99\n\n", $b->getString());
        $this->assertSame("432\nfoo\n\n\n1\n2.99\n432\nfoo\n\n\n1\n2.99\n\n", (string) $b);
        $this->assertSame("432\nfoo\n\n\n1\n2.99\n432\nfoo\n\n\n1\n2.99\n\n", $b->__toString());
    }
} 