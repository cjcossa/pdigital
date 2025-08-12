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
        Schema::create('group_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 200)->nullable();
            $table->uuid('group_id');
            $table->uuid('trace_id');
            $table->date('start_date')->nullable();  
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('status', false);
            $table->foreign('trace_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_cycles');
    }
};
