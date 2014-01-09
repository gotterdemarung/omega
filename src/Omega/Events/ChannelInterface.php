<?php

namespace Omega\Events;


interface ChannelInterface
{
    public function sendEvent(EventInterface $event);
} 