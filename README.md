# MOE-Laravel-Finance

Finance module for MOE ecosystem — Wallet, Payment, Refund.

## Installation

```bash
composer require moe/laravel-finance
php artisan vendor:publish --provider="Moe\Finance\FinanceServiceProvider" --tag="finance-config"
php artisan vendor:publish --provider="Moe\Finance\FinanceServiceProvider" --tag="finance-migrations"
php artisan migrate
```

## What's Included

### Models

| Model | Table | Description |
|-------|-------|-------------|
| `Wallet` | `finance_wallets` | User wallet with balance |
| `WalletTransaction` | `finance_wallet_transactions` | Transaction history |

### Services

| Service | Description |
|---------|-------------|
| `WalletService` | Credit, debit, transfer, transaction history |

### Contracts

| Contract | Description |
|----------|-------------|
| `WalletProviderInterface` | Interface for wallet operations |
| `RefundableInterface` | Interface for refundable models |

## Usage

### Credit Wallet

```php
use Moe\Finance\Services\WalletService;

$walletService = app(WalletService::class);
$walletService->credit(100000, 'topup', 'Top up via bank transfer');
```

### Debit Wallet

```php
$walletService->debit(50000, 'payment', 'Order #123');
```

### Transfer

```php
$walletService->transfer($fromWallet, $toWallet, 25000, 'Transfer to friend');
```

### Check Balance

```php
$balance = $walletService->getBalance();
$hasEnough = $walletService->hasSufficientBalance(100000);
```

### Get Transactions

```php
$transactions = $walletService->getTransactions(25);
```

## Config

```php
// config/finance.php
return [
    'currency' => 'IDR',
    'tables' => [
        'wallets' => 'finance_wallets',
        'wallet_transactions' => 'finance_wallet_transactions',
    ],
];
```

## Requirements

- PHP ^8.2
- Laravel ^12.0|^13.0
- `moe/laravel-core`

## License

MIT
