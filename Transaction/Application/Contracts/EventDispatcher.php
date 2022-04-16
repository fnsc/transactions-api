<?php

namespace Transaction\Application\Contracts;

interface EventDispatcher
{
    public function dispatch(EventInterface $event): void;
}
