<?php

namespace Transaction\Infra\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Application\Contracts\AuthenticatedUserAdapter as AuthenticatedUserAdapterInterface;
use Transaction\Infra\Adapters\AuthenticatedUser;

class AuthenticatedUserServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthenticatedUserAdapterInterface::class, AuthenticatedUser::class);
    }
}
