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
            $table->uuid('id')->primary();
            $table->string('wallet_code');
            $table->string('transaction_reference')->nullable();
            $table->string('phone_number');
            $table->decimal('amount', 12, 2);
            $table->string('operation');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status', false);
            $table->date('processed_at')->nullable();
            $table->date('wallet_processed_date')->nullable();
            $table->uuid('group_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('trace_id');
            $table->uuid('trace_update_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('trace_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('trace_update_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
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
