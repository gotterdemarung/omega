<?php

namespace Omega\Events;


class StringKeyAmountEvent extends AbstractEvent
{
    /**
     * @var string
     */
    protected $_key;

    /**
     * @var int|float
     */
    protected $_amount;

    /**
     * Creates new count event
     *
     * @param object    $sender
     * @param string    $key
     * @param int|float $increment
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($sender, $key, $increment)
    {
        parent::__construct($sender);

        if ($key === null) {
            throw new \InvalidArgumentException('Key is null');
        }
        if ($increment === null
            || (!is_int($increment)
            && !is_float($increment))
        ) {
            throw new \InvalidArgumentException(
                'Amount not valid. Received ' . $increment
            );
        }

        $this->_key = '' . $key;
        $this->_amount = $increment;
    }

    /**
     * Returns Amount
     *
     * @return float|int
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Returns key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }



} 