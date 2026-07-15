<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_wallets', function (Blueprint $table) {
            $table->id();
            $table->morphs('walletable');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('finance_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('finance_wallets')->cascadeOnDelete();
            $table->string('type', 50);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->nullableMorphs('reference');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['wallet_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_wallet_transactions');
        Schema::dropIfExists('finance_wallets');
    }
};
