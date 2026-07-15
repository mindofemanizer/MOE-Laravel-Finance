<?php

declare(strict_types=1);

namespace Moe\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'walletable_type',
        'walletable_id',
        'balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('finance.tables.wallets', 'finance_wallets');
    }

    public function walletable(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }

    public function getBalance(): float
    {
        return (float) $this->balance;
    }

    public function credit(float $amount, string $type, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $type, $description) {
            $balanceBefore = $this->getBalance();

            $this->increment('balance', $amount);

            return $this->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceBefore + $amount,
            ]);
        });
    }

    public function debit(float $amount, string $type, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $type, $description) {
            $balanceBefore = $this->getBalance();

            if ($balanceBefore < $amount) {
                throw new \Moe\Core\Exceptions\InsufficientBalance(
                    "Saldo tidak mencukupi. Dibutuhkan: {$amount}, Tersedia: {$balanceBefore}"
                );
            }

            $this->decrement('balance', $amount);

            return $this->transactions()->create([
                'type' => $type,
                'amount' => -$amount,
                'description' => $description,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceBefore - $amount,
            ]);
        });
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->getBalance() >= $amount;
    }
}
