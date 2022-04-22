<?php

namespace Transaction\Infra\Adapters;

use Transaction\Application\Contracts\EventDispatcher as EventDispatcherInterface;
use Transaction\Application\Contracts\EventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void
    {
        event($event);
    }
}
