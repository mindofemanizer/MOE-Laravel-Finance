<?php

declare(strict_types=1);

namespace Moe\Finance\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Moe\Commerce\Events\OrderStatusChanged;
use Moe\Finance\Models\Wallet;

class CreditMerchantOnOrderCompleted
{
    /**
     * Handle the event.
     *
     * @param \Moe\Commerce\Events\OrderStatusChanged $event
     */
    public function handle(OrderStatusChanged $event): void
    {
        try {
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

            $amount = (float) $event->order->total;
            $fee = config('finance.platform_fee', 0);
            $netAmount = $amount - ($amount * $fee);

            $wallet = Wallet::firstOrCreate(
                [
                    'walletable_type' => User::class,
                    'walletable_id' => $store->user_id,
                ],
                [
                    'balance' => 0,
                    'currency' => config('finance.currency', 'IDR'),
                ]
            );

            $wallet->credit($netAmount, 'commission', "Pendapatan order {$event->order->order_number}");
        } catch (\Throwable $e) {
            Log::error('[finance] CreditMerchantOnOrderCompleted failed: '.$e->getMessage(), [
                'order_id' => $event->order?->id,
            ]);
        }
    }
}
