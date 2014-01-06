<?php

namespace Omega\IO;

/**
 * Class StdOut
 * Standard output implementation of PrintWriterInterface
 *
 * @package Omega\IO
 */
class StdOut implements HTTPPrintWriterInterface
{

    /**
     * Prints HTTP header
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws \BadMethodCallException if headers already sent
     */
    public function writeHTTPHeader($key, $value)
    {
        if (headers_sent()) {
            throw new \BadMethodCallException('Headers already sent');
        }

        header($key . ': ' . $value);
    }

    /**
     * Prints an object to standard output
     *
     * @param mixed $object
     * @return void
     */
    public function write($object)
    {
        echo $object;
    }

    /**
     * Prints an object  to standard output and puts a newline character after it
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

}