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
    public static function fullMonth(Instant $instant = null)
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
        if ($this->getEnd()->toFloat() < $this->getEnd()->toFloat()) {
            return new static($this->getEnd(), $this->getBegin());
        }

        return $this;
    }

    /**
     * Returns rounded amount of days between begin and end
     *
     * @return float
     */
    public function getDaysCountRounded()
    {
        return round(($this->getEnd()->toFloat() - $this->getBegin()->toFloat()) / 86400);
    }

    /**
     * Returns true, if current period points to whole month
     *
     * @return bool
     */
    public function isFullMonth()
    {
        return $this->getBegin()->isMidnight()
        && $this->getEnd()->isDayEnd()
        && $this->getBegin()->isSameMonth($this->getEnd());
    }

    /**
     * Magic method
     *
     * @return string
     */
    public function __toString()
    {
        return '[' . $this->getBegin() . ' - ' . $this->getEnd() . ']';
    }

} 