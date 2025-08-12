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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->uuid('user_id');
            $table->uuid('group_id')->nullable();
            $table->uuid('user_wallet_id');
            $table->uuid('approved_by');
            $table->uuid('group_cycle_id');
            $table->decimal('amount', 12, 2);
            $table->decimal('interest', 12, 2);
            $table->text('description')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->unsignedTinyInteger('status', false);
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->foreign('user_wallet_id')->references('id')->on('group_user_wallets')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_cycle_id')->references('id')->on('group_cycles')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
