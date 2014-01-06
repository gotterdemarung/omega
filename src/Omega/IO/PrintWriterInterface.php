<?php

namespace Omega\IO;

/**
 * Interface for all objects, able to print somewhere
 *
 * @package Omega\IO
 */
interface PrintWriterInterface
{
    /**
     * Prints an object
     *
     * @param mixed $object
     * @return void
     */
    public function write($object);

    /**
     * Prints an object and puts a newline character after it
     *
     * @param mixed|null $object
     * @return void
     */
    public function writeln($object = null);
}