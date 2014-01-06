<?php

namespace Omega\IO;

/**
 * Class StringBuffer
 * Buffer implementation of PrintWriterInterface
 *
 * @package Omega\IO
 */
class StringBuffer implements PrintWriterInterface
{
    /**
     * Storage
     *
     * @var string
     */
    private $_buffer = '';

    /**
     * Prints an object to string buffer
     *
     * @param mixed $object
     * @return void
     */
    public function write($object)
    {
        if ($object === null) {
            // Skipping NULLs
            return;
        }

        $this->_buffer .= (string) $object;
    }

    /**
     * Prints an object to string buffer and puts a newline character after it
     *
     * @param mixed|null $object
     * @return void
     */
    public function writeln($object = null)
    {
        if ($object !== null) {
            $this->write($object);
        }
        $this->write(PHP_EOL);
    }

    /**
     * Returns contents of the buffer
     *
     * @return string
     */
    public function getString()
    {
        return $this->_buffer;
    }

    /**
     * Returns contents of the buffer
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getString();
    }
}