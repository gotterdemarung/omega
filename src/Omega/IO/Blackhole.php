<?php

namespace Omega\IO;

/**
 * Class Blackhole
 * Blackhole implementation of PrintWriter
 *
 * @package Omega\IO
 */
class Blackhole implements PrintWriterInterface
{
    /**
     * Prints a string
     *
     * @param mixed $object
     * @return void
     */
    public function write($object)
    {
        // Does nothing
    }

    /**
     * Prints a string and puts a newline character after it
     *
     * @param mixed|null $object
     * @return void
     */
    public function writeln($object = null)
    {
        // Does nothing
    }
}