<?php

namespace Moe\Finance\Tests;

use Moe\Core\Exceptions\InsufficientBalance;
use Moe\Finance\Models\Wallet;
use Moe\Finance\Models\WalletTransaction;

class WalletServiceTest extends TestCase
{
    public function test_can_create_wallet()
    {
        $wallet = Wallet::create([
            'walletable_type' => 'App\\Models\\User',
            'walletable_id' => 1,
            'balance' => 0,
            'currency' => 'IDR',
        ]);

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals(0, $wallet->balance);
    }

    public function test_can_credit_wallet()
    {
        $wallet = Wallet::create([
            'walletable_type' => 'App\\Models\\User',
            'walletable_id' => 1,
            'balance' => 0,
            'currency' => 'IDR',
        ]);

        $transaction = $wallet->credit(50000, 'topup', 'Topup saldo');

        $this->assertInstanceOf(WalletTransaction::class, $transaction);
        $this->assertEquals(50000, $transaction->amount);
        $this->assertEquals(50000, $wallet->fresh()->balance);
    }

    public function test_can_debit_wallet()
    {
        $wallet = Wallet::create([
            'walletable_type' => 'App\\Models\\User',
            'walletable_id' => 1,
            'balance' => 100000,
            'currency' => 'IDR',
        ]);

        $transaction = $wallet->debit(30000, 'payment', 'Pembayaran');

        $this->assertEquals(-30000, $transaction->amount);
        $this->assertEquals(70000, $wallet->fresh()->balance);
    }

    public function test_cannot_debit_insufficient_balance()
    {
        $this->expectException(InsufficientBalance::class);

        $wallet = Wallet::create([
            'walletable_type' => 'App\\Models\\User',
            'walletable_id' => 1,
            'balance' => 10000,
            'currency' => 'IDR',
        ]);

        $wallet->debit(50000, 'payment', 'Pembayaran');
    }

    public function test_has_sufficient_balance()
    {
        $wallet = Wallet::create([
            'walletable_type' => 'App\\Models\\User',
            'walletable_id' => 1,
            'balance' => 100000,
            'currency' => 'IDR',
        ]);

        $this->assertTrue($wallet->hasSufficientBalance(50000));
        $this->assertFalse($wallet->hasSufficientBalance(150000));
    }
}
