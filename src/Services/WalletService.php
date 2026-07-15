<?php

namespace Moe\Finance\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Moe\Core\Base\BaseService;
use Moe\Core\Exceptions\InsufficientBalance;
use Moe\Finance\Contracts\WalletProviderInterface;
use Moe\Finance\Models\Wallet;
use Moe\Finance\Models\WalletTransaction;

class WalletService extends BaseService implements WalletProviderInterface
{
    /**
     * Get or create wallet for a model.
     */
    public function getWallet(): ?Model
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return $user->wallet()->firstOrCreate([
            'walletable_type' => get_class($user),
            'walletable_id' => $user->id,
        ], [
            'balance' => 0,
            'currency' => config('finance.currency', 'IDR'),
        ]);
    }

    /**
     * Get current balance.
     */
    public function getBalance(): float
    {
        $wallet = $this->getWallet();

        return $wallet?->getBalance() ?? 0;
    }

    /**
     * Credit amount to wallet.
     */
    public function credit(float $amount, string $type, ?string $description = null): WalletTransaction
    {
        $wallet = $this->getWallet();

        if (! $wallet) {
            throw new \RuntimeException('Wallet not found');
        }

        return DB::transaction(function () use ($wallet, $amount, $type, $description) {
            return $wallet->credit($amount, $type, $description);
        });
    }

    /**
     * Debit amount from wallet.
     */
    public function debit(float $amount, string $type, ?string $description = null): WalletTransaction
    {
        $wallet = $this->getWallet();

        if (! $wallet) {
            throw new \RuntimeException('Wallet not found');
        }

        return DB::transaction(function () use ($wallet, $amount, $type, $description) {
            return $wallet->debit($amount, $type, $description);
        });
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->getBalance() >= $amount;
    }

    /**
     * Transfer between wallets.
     */
    public function transfer(Wallet $from, Wallet $to, float $amount, ?string $description = null): void
    {
        DB::transaction(function () use ($from, $to, $amount, $description) {
            $from->debit($amount, 'transfer_out', $description);
            $to->credit($amount, 'transfer_in', $description);
        });
    }

    /**
     * Get transaction history.
     */
    public function getTransactions(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        $wallet = $this->getWallet();

        if (! $wallet) {
            return collect();
        }

        return $wallet->transactions()
            ->latest()
            ->limit($limit)
            ->get();
    }
}
