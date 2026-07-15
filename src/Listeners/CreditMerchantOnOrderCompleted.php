<?php

declare(strict_types=1);

namespace Moe\Finance\Listeners;

use Moe\Commerce\Events\OrderStatusChanged;
use Moe\Finance\Models\Wallet;

class CreditMerchantOnOrderCompleted
{
    public function handle(OrderStatusChanged $event): void
    {
        if ($event->newStatus !== 'completed') {
            return;
        }

        if (! $event->order) {
            return;
        }

        $store = $event->order->store;
        if (! $store || ! $store->user_id) {
            return;
        }

        $wallet = Wallet::where('user_id', $store->user_id)->first();
        if (! $wallet) {
            return;
        }

        $amount = (float) $event->order->total;
        $fee = config('finance.platform_fee', 0);
        $netAmount = $amount - ($amount * $fee);

        $wallet->credit($netAmount, 'commission', "Pendapatan order {$event->order->order_number}");
    }
}
