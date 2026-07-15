<?php

declare(strict_types=1);

namespace Moe\Finance\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RefundableInterface
{
    public function refund(float $amount, ?string $reason = null): Model;
    public function canRefund(): bool;
    public function getRefundableAmount(): float;
}
