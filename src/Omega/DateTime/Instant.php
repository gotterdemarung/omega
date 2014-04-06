<?php

namespace Omega\DateTime;

/**
 * Class Instant
 *
 * High precision unix timestamp
 *
 * @package Omega\Type
 */
class Instant
{
    /**
     * Value of timestamp
     *
     * @var float
     */
    protected $_value;


    /**
     * Return true if provided string is valid timestamp value
     *
     * @param string $timestampString
     * @return bool
     */
    static public function isValidStringTimeStamp($timestampString)
    {
        // Special assert for PHP DateTime object
        // because it cannot be cast to string
        if ($timestampString instanceof \DateTime) {
            return false;
        }

        // String cast
        $timestampString = (string) $timestampString;

        // Validating
        $answer = ((string) (float) $timestampString === $timestampString)
            && ($timestampString <= PHP_INT_MAX)
            && ($timestampString >= ~PHP_INT_MAX);

        return $answer;
    }

    /**
     * Constructs new TimestampUTC object on provided values
     *
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param int $day
     * @param int $month
     * @param int $year
     * @return Instant
     */
    static public function create(
        $hour,
        $minute,
        $second,
        $day,
        $month,
        $year
    )
    {
        $reset = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        date_default_timezone_set($reset);

        return new Instant($timestamp);
    }

    /**
     * Returns current time
     *
     * @return Instant
     */
    static public function now()
    {
        return new self(microtime(true));
    }

    /**
     * Constructor
     *
     * @param int|float|string|\DateTime|Instant $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        // Validating value
        if (empty($value)) {
            $this->_value = 0.0;
        } else if (is_float($value) || is_double($value) || is_int($value)) {
            // Plain numeric timestamp
            $this->_value = (float) $value;
        } else if ($value instanceof Instant) {
            // Own type
            $this->_value = $value->_value;
        } else if ($value instanceof \DateTime) {
            // PHP DateTime object
            $this->_value = (float) $value->getTimestamp();
        } else if (self::isValidStringTimeStamp($value)) {
            // Int representation
            $this->_value = (float) $value;
        } else {
            // strtotime
            $this->_value = (float) strtotime($value);
        }
    }

    /**
     * Returns value of timestamp
     *
     * @return float
     */
    public function toFloat()
    {
        return (float) $this->_value;
    }

    /**
     * Returns int part of timestamp
     *
     * @return int
     */
    public function toInt()
    {
        return (int) $this->_value;
    }

    /**
     * Returns new Instant, representing begin of day (midnight)
     *
     * @return Instant
     */
    public function getDayBegin()
    {
        return new static(strtotime('today', $this->toInt()));
    }

    /**
     * Returns new Instant, representing begin of day (midnight)
     *
     * @return Instant
     */
    public function getMidnight()
    {
        return new static(strtotime('today', $this->toInt()));
    }

    /**
     * Returns new Instant, representing end of day
     *
     * @return Instant
     */
    public function getDayEnd()
    {
        return new static(strtotime('tomorrow', $this->toInt()) - 1);
    }

    /**
     * Returns new Instant for first day of month
     * Time not changed, only day of month
     *
     * @return Instant
     */
    public function getMonthBeginDay()
    {
        return new static(strtotime('first day of this month', $this->toInt()));
    }

    /**
     * Returns new Instant for last day of month
     * Time not changed, only day of month
     *
     * @return Instant
     */
    public function getMonthEndDay()
    {
        return new static(strtotime('last day of this month', $this->toInt()));
    }

    /**
     * Returns date in specific format
     *
     * @param string $format http://php.net/manual/en/function.date.php
     * @return bool|string
     */
    public function format($format)
    {
        return date($format, $this->toInt());
    }


    /**
     * Returns date in MySQL TIMESTAMP format string
     *
     * @return string
     */
    public function toMySQLDateTime()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Returns date in MySQL DATE format string
     *
     * @return string
     */
    public function toMySQLDate()
    {
        return $this->format('Y-m-d');
    }

    /**
     * Returns true if current object timestamp bigger then provided
     *
     * @param mixed|Instant $object
     * @return bool
     */
    public function isBiggerThen($object)
    {
        $object = new self($object);
        return $this->_value > $object->_value;
    }

    /**
     * Returns true this current object timestamp lesser then provided
     *
     * @param mixed|Instant $object
     * @return bool
     */
    public function isLesserThen($object)
    {
        $object = new self($object);
        return $this->_value < $object->_value;
    }

    /**
     * Returns true if provided object or type contains same timestamp
     *
     * @param mixed|Instant $another
     * @return bool
     */
    public function equals($another)
    {
        if ($another === null) {
            return false;
        }

        $another = new self($another);
        return $this->_value == $another->_value;
    }

    /**
     * Magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toMySQLDateTime();
    }
}