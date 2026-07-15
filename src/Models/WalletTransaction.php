<?php

declare(strict_types=1);

namespace Moe\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransaction extends Model
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('finance.tables.wallet_transactions', 'finance_wallet_transactions');
    }

    /**
     * Get the wallet relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the reference model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the transaction is a credit.
     *
     * @return bool
     */
    public function isCredit(): bool
    {
        return $this->amount > 0;
    }

    /**
     * Check if the transaction is a debit.
     *
     * @return bool
     */
    public function isDebit(): bool
    {
        return $this->amount < 0;
    }
}
