<?php

namespace Omega\Type;

/**
 * Class TimestampUTC
 * High precision unix timestamp
 * Works on UTC time
 *
 * @package Omega\Type
 */
class TimestampUTC
{
    /**
     * Value of timestamp
     *
     * @var float
     */
    protected $_value;

    /**
     * Amount of digits after comma
     * Default 6 (for microtime)
     *
     * @var int
     */
    protected $_precision;

    /**
     * Constructs new TimestampUTC object on provided values
     *
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param int $month
     * @param int $day
     * @param int $year
     * @return TimestampUTC
     */
    static public function mkTime(
        $hour,
        $minute,
        $second,
        $month,
        $day,
        $year
    )
    {
        $reset = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        date_default_timezone_set($reset);

        return new TimestampUTC($timestamp);
    }

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
     * Returns current time
     *
     * @param int $precision
     * @return TimestampUTC
     */
    static public function now($precision = 6)
    {
        return new self(microtime(true), $precision);
    }

    /**
     * Constructor
     *
     * @param int|float|string|\DateTime|TimestampUTC $value
     * @param int|null                                $precision
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value, $precision = null)
    {
        // Validating precision
        if ($precision === null) {
            $this->_precision = 6;
        } else {
            if ($precision < 0) {
                throw new \InvalidArgumentException(
                    'Precision should be in range 0-9'
                );
            }
            $this->_precision = (int) $precision;
        }

        // Validating value
        if (empty($value)) {
            $this->_value = 0.0;
        } else if (is_float($value) || is_double($value) || is_int($value)) {
            // Plain numeric timestamp
            $this->_value = (float) $value;
        } else if ($value instanceof TimestampUTC) {
            // Own type
            $this->_value = $value->_value;
            if ($precision === null) {
                // No precision set, copying from value
                $this->_precision = $value->_precision;
            }
        } else if ($value instanceof \DateTime) {
            // PHP DateTime object
            $this->_value = (float) $value->getTimestamp();
        } else if (self::isValidStringTimeStamp($value)) {
            // String representation
            $this->_value = (float) $value;
        } else {
            // strtotime
            $reset = date_default_timezone_get();
            date_default_timezone_set('UTC');
            $this->_value = (float) strtotime($value);
            date_default_timezone_set($reset);
        }

        $this->_trim();
    }

    /**
     * Trims value to expected precision
     */
    protected function _trim()
    {
        $this->_value = (float) $this->getString();
    }

    /**
     * Returns after comma part of object
     *
     * @return float
     */
    protected function _getDetail()
    {
        return $this->_value - intval($this->_value);
    }

    /**
     * {@inheritdoc}
     */
    public function equals($object)
    {
        if ($object === null) {
            return false;
        }
        if (!($object instanceof TimestampUTC)) {
            $object = new TimestampUTC($object);
        }

        return $this->_value == $object->_value;
    }

    /**
     * Returns date in specific format
     *
     * @param string $format http://php.net/manual/en/function.date.php
     * @return bool|string
     */
    public function format($format)
    {
        $reset = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $string = date($format, $this->getUnixTimestamp());
        date_default_timezone_set($reset);

        return $string;
    }

    /**
     * Returns new TimestampPrecise, representing begin of day
     *
     * @return TimestampUTC
     */
    public function getDayBegin()
    {
        return self::mkTime(
            0,
            0,
            0,
            $this->format('m'),
            $this->format('d'),
            $this->format('Y')
        );
    }

    /**
     * Returns new TimestampPrecise, representing end of day
     *
     * @return TimestampUTC
     */
    public function getDayEnd()
    {
        return self::mkTime(
            23,
            59,
            59,
            $this->format('m'),
            $this->format('d'),
            $this->format('Y')
        );
    }


    /**
     * Returns value of timestamp
     *
     * @return float
     */
    public function getFloat()
    {
        return $this->_value;
    }

    /**
     * Returns int part of timestamp
     *
     * @return int
     */
    public function getInt()
    {
        return (int) $this->_value;
    }

    /**
     * Returns date in MySQL TIMESTAMP format string
     *
     * @return string
     */
    public function getMySQLDateTime()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Returns date in MySQL DATE format string
     *
     * @return string
     */
    public function getMySQLDate()
    {
        return $this->format('Y-m-d');
    }


    /**
     * Return unix timestamp (integer)
     *
     * @return int
     */
    public function getUnixTimestamp()
    {
        return $this->getInt();
    }

    /**
     * Returns string representation of timestamp
     * Precision is optional and if omitted, method uses
     * object's precision
     *
     * @param int|null $precision
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getString($precision = null)
    {
        if ($precision === null) {
            $precision = $this->_precision;
        } else {
            if ($precision < 0) {
                throw new \InvalidArgumentException(
                    'Precision should be in range 0-9'
                );
            }
            $precision = (int) $precision;
        }

        if ($precision === 0) {
            return (string) intval($this->_value);
        } else {
            return sprintf("%.{$precision}f", $this->_value);
        }
    }

    /**
     * Returns true if current object timestamp bigger then provided
     *
     * @param mixed|TimestampUTC $object
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
     * @param mixed|TimestampUTC $object
     * @return bool
     */
    public function isLesserThen($object)
    {
        $object = new self($object);
        return $this->_value < $object->_value;
    }


    /**
     * {@inheritdoc}
     */
    public function toJSON()
    {
        return $this->getString();
    }


}