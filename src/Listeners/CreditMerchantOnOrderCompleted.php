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

            $wallet = Wallet::query()
                ->where('walletable_type', User::class)
                ->where('walletable_id', $store->user_id)
                ->first();
            if (! $wallet) {
                return;
            }

            $amount = (float) $event->order->total;
            $fee = config('finance.platform_fee', 0);
            $netAmount = $amount - ($amount * $fee);

            $wallet->credit($netAmount, 'commission', "Pendapatan order {$event->order->order_number}");
        } catch (\Throwable $e) {
            Log::error('[finance] CreditMerchantOnOrderCompleted failed: '.$e->getMessage(), [
                'order_id' => $event->order?->id,
            ]);
        }
    }
}
