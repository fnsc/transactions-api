<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     */
    public final const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->getUserRoutes();
            $this->getTransactionRoutes();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for(
            'api',
            fn(Request $request) => Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip())
        );
    }

    private function getUserRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('Transaction/routes/users_api.php'));
    }

    private function getTransactionRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('Transaction/routes/transactions_api.php'));
    }
}
