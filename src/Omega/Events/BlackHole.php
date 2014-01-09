<?php

namespace Omega\Events;


class BlackHole implements ChannelInterface
{
    public function sendEvent(EventInterface $event)
    {
        // do nothing
    }

} 