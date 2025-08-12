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
        Schema::create('interest_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('group_id');
            $table->uuid('role_id');
            $table->uuid('group_cycle_id');
            $table->uuid('interest_mode_id');
            $table->uuid('trace_id');
            $table->decimal('fee', 10, 2);
            $table->unsignedTinyInteger('status', false);
            $table->text('description')->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->foreign('trace_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('group_cycle_id')->references('id')->on('group_cycles')->onDelete('restrict');
            $table->foreign('interest_mode_id')->references('id')->on('roles')->onDelete('restrict');
            $table->unique(['interest_mode_id', 'group_id', 'trace_id', 'group_cycle_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_rates');
    }
};
