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
        Schema::table('group_cycles', function (Blueprint $table) {
            $table->uuid('periodicity_id')->nullable()->after('end_date');
            $table->foreign('periodicity_id')
                  ->references('id')
                  ->on('periodicities')
                  ->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_cycles', function (Blueprint $table) {
            //
        });
    }
};
