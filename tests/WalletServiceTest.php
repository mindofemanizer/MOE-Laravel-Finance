<?php

namespace Moe\Finance\Tests;

use Moe\Finance\Models\Wallet;
use Moe\Finance\Services\WalletService;

class WalletServiceTest extends TestCase
{
    private WalletService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WalletService();
    }

    public function test_can_create_wallet()
    {
        $wallet = $this->service->createWallet(1, 'IDR');

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals(1, $wallet->user_id);
        $this->assertEquals(0, $wallet->balance);
    }

    public function test_can_credit_wallet()
    {
        $wallet = $this->service->createWallet(1, 'IDR');
        $transaction = $this->service->credit($wallet, 50000, 'topup', 'Topup saldo');

        $this->assertEquals(50000, $transaction->amount);
        $this->assertEquals('credit', $transaction->type);
        $this->assertEquals(50000, $wallet->fresh()->balance);
    }

    public function test_can_debit_wallet()
    {
        $wallet = $this->service->createWallet(1, 'IDR');
        $this->service->credit($wallet, 100000, 'topup', 'Topup');
        $this->service->debit($wallet, 30000, 'payment', 'Pembayaran');

        $this->assertEquals(70000, $wallet->fresh()->balance);
    }

    public function test_cannot_debit_insufficient_balance()
    {
        $this->expectException(\Exception::class);

        $wallet = $this->service->createWallet(1, 'IDR');
        $this->service->debit($wallet, 50000, 'payment', 'Pembayaran');
    }

    public function test_has_sufficient_balance()
    {
        $wallet = $this->service->createWallet(1, 'IDR');
        $this->service->credit($wallet, 100000, 'topup', 'Topup');

        $this->assertTrue($this->service->hasSufficientBalance($wallet, 50000));
        $this->assertFalse($this->service->hasSufficientBalance($wallet, 150000));
    }
}
