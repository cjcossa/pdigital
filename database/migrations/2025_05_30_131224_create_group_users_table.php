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
        Schema::create('group_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('group_id');
            $table->uuid('role_id');
            $table->uuid('group_cycle_id');
            $table->uuid('trace_id');
            $table->unsignedTinyInteger('status', false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('trace_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('group_cycle_id')->references('id')->on('group_cycles')->onDelete('restrict');
            $table->unique(['user_id', 'group_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_users');
    }
};
