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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 150);
            $table->string('last_name', 150);
            $table->string('primary_phone', 9)->unique();
            $table->json('phones')->nullable();  
            $table->json('doc_details')->nullable();
            $table->json('beneficiaries')->nullable();
            $table->string('pin', 256);
            $table->uuid('trace_id');
            $table->uuid('trace_update_id')->nullable();
            $table->unsignedTinyInteger('status', false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
