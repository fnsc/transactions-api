<?php

namespace Transaction\Infra\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Application\Contracts\LoginAdapter as LoginAdapterInterface;
use Transaction\Domain\Contracts\UserRepository as UserRepositoryInterface;
use Transaction\Infra\Adapters\LoginAdapter;
use Transaction\Infra\Repositories\Account as AccountRepository;
use Transaction\Infra\Repositories\User as UserRepository;

class LoginAdapterServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginAdapterInterface::class, LoginAdapter::class);
    }
}
