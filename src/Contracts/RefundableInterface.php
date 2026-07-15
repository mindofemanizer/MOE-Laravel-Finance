<?php

declare(strict_types=1);

namespace Moe\Finance\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RefundableInterface
{
    /**
     * Process a refund.
     *
     * @param float $amount
     * @param string|null $reason
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function refund(float $amount, ?string $reason = null): Model;

    /**
     * Check if a refund can be processed.
     *
     * @return bool
     */
    public function canRefund(): bool;

    /**
     * Get the refundable amount.
     *
     * @return float
     */
    public function getRefundableAmount(): float;
}
