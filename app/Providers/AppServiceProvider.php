<?php

namespace App\Providers;

use Fnsc\FiscalDoc\Validator as RegistrationNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private array $rules = [
        RegistrationNumber::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRules();
    }

    private function registerRules(): void
    {
        foreach ($this->rules as $rule) {
            $alias = (new $rule())->getAlias();
            Validator::extend($alias, $rule . '@passes');
        }
    }
}
