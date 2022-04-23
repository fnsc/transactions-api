<?php

namespace Transaction\Infra\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Application\Contracts\EventDispatcher as EventDispatcherInterface;
use Transaction\Infra\Adapters\EventDispatcher;

class EventDispatcherServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);
    }
}
