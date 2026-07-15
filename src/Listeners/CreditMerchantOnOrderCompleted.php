<?php

namespace Moe\Finance\Listeners;

use Moe\Commerce\Events\OrderStatusChanged;
use Moe\Finance\Models\Wallet;
use Moe\Finance\Models\WalletTransaction;

class CreditMerchantOnOrderCompleted
{
    public function handle(OrderStatusChanged $event): void
    {
        if ($event->newStatus !== 'completed') {
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

        $wallet->transactions()->create([
            'type' => 'credit',
            'amount' => $netAmount,
            'description' => "Pendapatan order {$event->order->order_number}",
            'reference_type' => 'order',
            'reference_id' => $event->order->id,
            'status' => 'completed',
        ]);
    }
}
