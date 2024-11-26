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
        Schema::create('games_histories', function (Blueprint $table) {
            $table->id();
            $table->string('MemberName')->nullable();
            $table->string('MemberID')->nullable();
            $table->string('OperatorID')->nullable();
            $table->string('ProductID')->nullable();
            $table->string('ProviderID')->nullable();
            $table->string('ProviderLineID')->nullable();
            $table->string('WagerID')->nullable();
            $table->string('CurrencyID')->nullable();
            $table->string('GameType')->nullable();
            $table->string('GameID')->nullable();
            $table->string('GameRoundID')->nullable();
            $table->string('ValidBetAmount')->nullable();
            $table->string('BetAmount')->nullable();
            $table->string('TransactionAmount')->nullable();
            $table->string('TransactionID')->nullable();
            $table->string('PayoutAmount')->nullable();
            $table->string('PayoutDetail')->nullable();
            $table->string('CommissionAmount')->nullable();
            $table->string('JackpotAmount')->nullable();
            $table->string('SettlementDate')->nullable();
            $table->string('JPBet')->nullable();
            $table->string('Status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games_histories');
    }
};
