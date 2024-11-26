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
        Schema::create('game_lists', function (Blueprint $table) {
            $table->id();
            $table->string('Provider')->nullable();
            $table->string('GameName')->nullable();
            $table->string('GameCode')->nullable();
            $table->string('GameType')->nullable();
            $table->string('ProviderCode')->nullable();
            $table->string('GameImage')->nullable();
            $table->string('Category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_lists');
    }
};
