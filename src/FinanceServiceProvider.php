<?php

namespace Moe\Finance;

use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/finance.php', 'finance');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'finance');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'finance');

        $this->publishes([
            __DIR__.'/../config/finance.php' => config_path('finance.php'),
        ], 'finance-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'finance-migrations');
    }
}
