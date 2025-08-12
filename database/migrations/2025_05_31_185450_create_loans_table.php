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
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('user_request_id')->nullable();
            $table->uuid('group_id')->nullable();
            $table->uuid('group_cycle_id')->nullable();
            $table->uuid('interest_rate_id');
            $table->uuid('guarantor_id')->nullable();
            $table->uuid('trace_id');
            $table->decimal('amount', 12, 2);
            $table->string('phone_number');
            $table->string('type');//MM - Member to Member or GM - Group to Member
            $table->date('payment_date');
            $table->unsignedTinyInteger('status', false);
            $table->string('transaction_reference')->nullable();
            $table->text('description')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('user_request_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->foreign('guarantor_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::dropIfExists('loans');
    }
};
