<?php

namespace Transaction\Infra\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Domain\Contracts\AccountRepository as AccountRepositoryInterface;
use Transaction\Infra\Repositories\Account as AccountRepository;

class AccountServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
    }
}
