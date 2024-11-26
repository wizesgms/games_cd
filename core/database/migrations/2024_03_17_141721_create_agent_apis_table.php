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
        Schema::create('agent_apis', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('agentcode')->nullable();
            $table->string('secretkey')->nullable();
            $table->integer('status')->lenght(11)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_apis');
    }
};
