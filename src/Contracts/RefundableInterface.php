<?php

namespace Moe\Finance\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RefundableInterface
{
    public function refund(float $amount, ?string $reason = null): Model;
    public function canRefund(): bool;
    public function getRefundableAmount(): float;
}
