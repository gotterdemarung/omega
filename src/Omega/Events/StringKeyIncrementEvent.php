<?php

namespace Omega\Events;


class StringKeyIncrementEvent extends AbstractEvent
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int|float
     */
    protected $increment;

    /**
     * Creates new increment event
     *
     * @param object $sender
     * @param string $key
     * @param int    $increment
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($sender, $key, $increment = 1)
    {
        parent::__construct($sender);

        if ($key === null) {
            throw new \InvalidArgumentException('Key is null');
        }
        if ($increment === null || !is_int($increment)) {
            throw new \InvalidArgumentException('Increment not valid. Received ' . $increment);
        }

        $this->key = '' . $key;
        $this->increment = $increment;
    }

    /**
     * Returns count value
     *
     * @return float|int
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Returns key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}