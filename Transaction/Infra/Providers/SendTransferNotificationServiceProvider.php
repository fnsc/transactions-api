<?php

namespace Transaction\Infra\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Infra\Client\Notification;
use Transaction\Infra\Listeners\SendTransferNotification;

class SendTransferNotificationServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SendTransferNotification::class, function(Application $app) {
            $client = $app->make(Notification::class);

            return new SendTransferNotification($client);
        });
    }
}
