<?php

namespace Transaction\Infra\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Domain\Contracts\UserRepository as UserRepositoryInterface;
use Transaction\Infra\Repositories\Account as AccountRepository;
use Transaction\Infra\Repositories\User as UserRepository;

class UserServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, function (Application $app) {
            $accountRepository = $app->make(AccountRepository::class);

            return new UserRepository($accountRepository);
        });
    }
}
