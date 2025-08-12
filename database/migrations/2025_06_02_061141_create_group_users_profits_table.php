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
        Schema::create('group_users_profits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('group_id');
            $table->uuid('group_cycle_id');
            $table->uuid('user_id');
            $table->uuid('user_wallet_id');
            $table->uuid('trace_id');
            $table->decimal('savings_amount', 12, 2);
            $table->decimal('social_amount', 12, 2)->nullable();
            $table->decimal('interest_amount', 12, 2)->nullable();
            $table->date('calculated_at'); 
            $table->unsignedTinyInteger('status', false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->foreign('user_wallet_id')->references('id')->on('user_wallets')->onDelete('restrict');
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
        Schema::dropIfExists('group_users_profits');
    }
};
