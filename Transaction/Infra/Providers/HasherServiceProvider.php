<?php

namespace Transaction\Infra\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Domain\Contracts\PasswordHasher as PasswordHasherInterface;
use Transaction\Infra\Adapters\PasswordHasher;

class HasherServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PasswordHasherInterface::class,
            PasswordHasher::class
        );
    }
}
