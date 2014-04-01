<?php

namespace Omega\DateTime;

/**
 * Class Range
 *
 * Range between instants
 *
 * @package Omega\DateTime
 */
class Range
{
    /**
     * @var Instant
     */
    private $_begin;
    /**
     * @var Instant
     */
    private $_end;

    /**
     * Returns range for month for provided instant
     *
     * @param Instant $instant
     * @return Range
     */
    public static function thisMonth(Instant $instant = null)
    {
        if ($instant === null) {
            $instant = Instant::now();
        }

        return new static(
            $instant->getMonthBeginDay()->getMidnight(),
            $instant->getMonthEndDay()->getDayEnd()
        );
    }

    /**
     * Constructor
     *
     * @param Instant $begin
     * @param Instant $end
     */
    public function __construct(Instant $begin, Instant $end)
    {
        $this->_begin = $begin;
        $this->_end = $end;
    }

    /**
     * Returns begin of range
     *
     * @return Instant
     */
    public function getBegin()
    {
        return $this->_begin;
    }

    /**
     * Returns end of range
     *
     * @return Instant
     */
    public function getEnd()
    {
        return $this->_end;
    }

    /**
     * Returns period, where begin instant is before end
     *
     * @return Range
     */
    public function getNormalized()
    {
        if ($this->getEnd()->getFloat() < $this->getEnd()->getFloat()) {
            return new static($this->getEnd(), $this->getBegin());
        }

        return $this;
    }

} 