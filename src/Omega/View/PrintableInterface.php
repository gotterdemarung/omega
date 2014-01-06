<?php

namespace Omega\View;

use Omega\IO\PrintWriterInterface;

/**
 * Interface PrintableInterface
 *
 * @package Omega\View
 */
interface PrintableInterface
{
    /**
     * Outputs contents of object to provided PrintWriter
     *
     * @param PrintWriterInterface $target
     * @return mixed
     */
    public function printTo(PrintWriterInterface $target);

    /**
     * Returns string representation
     *
     * @return string
     */
    public function __toString();
}