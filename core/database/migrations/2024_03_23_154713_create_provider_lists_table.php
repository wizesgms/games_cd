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
        Schema::create('provider_lists', function (Blueprint $table) {
            $table->id();
            $table->string('ProviderName');
            $table->string('ProviderCode');
            $table->string('ProviderId');
            $table->integer('status')->lenght(11)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_lists');
    }
};
