<?php

namespace Omega\IO;

/**
 * Interface HTTPPrintWriterInterface
 * Extended print writer interface, containing methods for headers
 *
 * @package Omega\IO
 */
interface HTTPPrintWriterInterface extends PrintWriterInterface {
    /**
     * Prints HTTP header
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed
     */
    public function writeHTTPHeader($key, $value);
}