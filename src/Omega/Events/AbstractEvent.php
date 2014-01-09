<?php

namespace Omega\Events;


abstract class AbstractEvent implements EventInterface
{
    /**
     * @var int|float
     */
    private $_time;
    /**
     * @var object
     */
    private $_sender;

    /**
     * @param object         $sender
     * @param int|float|null $time
     * @throws \InvalidArgumentException
     */
    function __construct($sender, $time = null)
    {
        $this->_sender = $sender;
        if ($time === null) {
            $this->_time = microtime(true);
        } elseif (is_int($time)) {
            $this->_time = (float) $time;
        } elseif (is_float($time)) {
            $this->_time = $time;
        } else {
            throw new \InvalidArgumentException(
                'Wrong time format'
            );
        }
    }

    /**
     * @return object
     */
    public function getSender()
    {
        return $this->_sender;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->_time;
    }



} 