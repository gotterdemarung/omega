<?php

namespace Omega\Events;


class StringDebugEvent extends AbstractEvent
{

    private $_message;

    /**
     * @param object $sender
     * @param string $message
     */
    function __construct($sender, $message)
    {
        parent::__construct($sender, null);
        if ($message === null) {
            $this->_message = '';
        }

        $this->_message = (string) $message;
    }

    /**
     * Returns debug message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }
}