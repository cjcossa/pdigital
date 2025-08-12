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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->uuid('user_id');
            $table->uuid('group_id');
            $table->uuid('group_cycle_id');
            $table->string('wallet_code');
            $table->uuid('trace_id');
            $table->string('phone_number');
            $table->decimal('amount', 12, 2);
            $table->decimal('interest', 12, 2);
            $table->text('description')->nullable();
            $table->string('transaction_reference');
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->foreign('trace_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_cycle_id')->references('id')->on('group_cycles')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
