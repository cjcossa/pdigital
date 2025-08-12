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
        Schema::create('interest_modes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique(); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status', false);
            $table->uuid('trace_id');
            $table->uuid('trace_update_id');
            $table->foreign('trace_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('trace_update_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_modes');
    }
};
