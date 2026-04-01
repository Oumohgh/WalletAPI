<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('type', ['deposit', 'withdraw', 'transfer_out', 'transfer_in'])->nullable();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('amount')->default('0.00');
            $table->text('description')->nullable();
            $table->foreignId('receiver_wallet_id')->nullable()->constrained('wallets')->cascadeOnDelete();
            $table->foreignId('sender_wallet_id')->nullable()->constrained('wallets')->cascadeOnDelete();
            $table->float('balance_after')->default(0.00);
            $table->engine('innoDB');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');

    }
};
