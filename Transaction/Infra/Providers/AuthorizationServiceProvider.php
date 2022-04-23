<?php

namespace Transaction\Infra\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Application\Authorization\Service;
use Transaction\Infra\Client\Authorization;

class AuthorizationServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Service::class, function (Application $app) {
            $client = $app->make(Authorization::class);

            return new Service($client);
        });
    }
}
