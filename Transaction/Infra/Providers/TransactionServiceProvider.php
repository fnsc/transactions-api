<?php

namespace Transaction\Infra\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Application\Authorization\Service;
use Transaction\Domain\Contracts\TransactionRepository as TransactionRepositoryInterface;
use Transaction\Infra\Repositories\Account as AccountRepository;
use Transaction\Infra\Repositories\Transaction as TransactionRepository;

class TransactionServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            function(Application $app): TransactionRepository
            {
                $accountRepository = $app->make(AccountRepository::class);
                $authorizationService = $app->make(Service::class);

                return new TransactionRepository($accountRepository, $authorizationService);
            }
        );
    }
}
