<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migurations.
     */
    public function up(): void
    {
        Schema::create('loan_request_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_request_id');
            $table->uuid('approved_by');
            $table->date('approved_at');
            $table->unsignedTinyInteger('status', false);
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_request_approvals');
    }
};
