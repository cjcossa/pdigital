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
        Schema::create('periodicities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();   // daily, weekly, monthly
            $table->string('label');            // Diário, Semanal, Mensal
            $table->unsignedInteger('days');    // 1, 7, 30
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodicities');
    }
};
