<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\MoneyFormatter;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;

class MoneyServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            MoneyParser::class,
            function () {
                $currencies = new ISOCurrencies();

                return new DecimalMoneyParser($currencies);
            }
        );

        $this->app->bind(
            MoneyFormatter::class,
            function () {
                $currencies = new ISOCurrencies();

                return new DecimalMoneyFormatter($currencies);
            }
        );
    }

    public function provides()
    {
        return [
            MoneyParser::class,
            MoneyFormatter::class,
        ];
    }
}
