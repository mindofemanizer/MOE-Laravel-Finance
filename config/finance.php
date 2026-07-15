<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */
    'currency' => env('FINANCE_CURRENCY', 'IDR'),

    /*
    |--------------------------------------------------------------------------
    | Model Bindings
    |--------------------------------------------------------------------------
    */
    'models' => [

        'wallet' => Moe\Finance\Models\Wallet::class,

        'wallet_transaction' => Moe\Finance\Models\WalletTransaction::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    */
    'tables' => [

        'wallets' => 'finance_wallets',

        'wallet_transactions' => 'finance_wallet_transactions',

    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet Transaction Types
    |--------------------------------------------------------------------------
    */
    'transaction_types' => [

        'credit' => [
            'topup',
            'refund',
            'commission',
            'transfer_in',
            'adjustment',
        ],

        'debit' => [
            'payment',
            'withdrawal',
            'transfer_out',
            'adjustment',
        ],

    ],

];
