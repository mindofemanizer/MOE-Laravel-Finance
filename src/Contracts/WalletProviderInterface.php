<?php

declare(strict_types=1);

namespace Moe\Finance\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WalletProviderInterface
{
    /**
     * Get the wallet instance.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getWallet(): ?Model;

    /**
     * Get the current balance.
     *
     * @return float
     */
    public function getBalance(): float;

    /**
     * Credit the wallet.
     *
     * @param float $amount
     * @param string $type
     * @param string|null $description
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function credit(float $amount, string $type, ?string $description = null): Model;

    /**
     * Debit the wallet.
     *
     * @param float $amount
     * @param string $type
     * @param string|null $description
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function debit(float $amount, string $type, ?string $description = null): Model;

    /**
     * Check if the wallet has sufficient balance.
     *
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(float $amount): bool;
}
