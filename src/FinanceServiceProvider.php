<?php

declare(strict_types=1);

namespace Moe\Finance;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Moe\Commerce\Events\OrderStatusChanged;
use Moe\Finance\Listeners\CreditMerchantOnOrderCompleted;

class FinanceServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/finance.php', 'finance');
    }

    /**
     * Bootstrap any application services.
     */
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

        $this->registerEventListeners();
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        if (! class_exists(OrderStatusChanged::class)) {
            return;
        }

        Event::listen(
            OrderStatusChanged::class,
            CreditMerchantOnOrderCompleted::class
        );
    }
}
