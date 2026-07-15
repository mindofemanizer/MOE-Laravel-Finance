<?php

namespace Moe\Finance\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WalletProviderInterface
{
    public function getWallet(): ?Model;
    public function getBalance(): float;
    public function credit(float $amount, string $type, ?string $description = null): Model;
    public function debit(float $amount, string $type, ?string $description = null): Model;
    public function hasSufficientBalance(float $amount): bool;
}
