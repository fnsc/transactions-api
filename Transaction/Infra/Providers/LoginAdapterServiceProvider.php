<?php

namespace Transaction\Infra\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Transaction\Application\Contracts\LoginAdapter as LoginAdapterInterface;
use Transaction\Domain\Contracts\UserRepository as UserRepositoryInterface;
use Transaction\Infra\Adapters\LoginAdapter;

class LoginAdapterServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginAdapterInterface::class, function(Application $app) {
            $userRepository = $app->make(UserRepositoryInterface::class);

            return new LoginAdapter($userRepository);
        });
    }
}
