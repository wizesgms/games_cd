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
        Schema::create('wagers', function (Blueprint $table) {
            $table->id();
            $table->longText('WagerID');
            $table->string('MemberName');
            $table->longText('ProductID');
            $table->integer('GameType')->lenght(20);
            $table->integer('CurrencyID')->lenght(20);
            $table->string('GameID')->nullable();
            $table->string('GameRoundID')->nullable();
            $table->string('ValidBetAmount');
            $table->string('BetAmount');
            $table->string('JPBet')->nullable();
            $table->string('PayoutAmount');
            $table->string('CommisionAmount')->nullable();
            $table->string('JackpotAmount')->nullable();
            $table->timestamp('SettlementDate')->nullable();
            $table->integer('Status')->lenght(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wagers');
    }
};
