<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('group_cycles', 'contribution_amount')) {
            Schema::table('group_cycles', function (Blueprint $table) {
                $table->decimal('contribution_amount', 12, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('group_cycles', 'contribution_amount')) {
            Schema::table('group_cycles', function (Blueprint $table) {
                $table->dropColumn('contribution_amount');
            });
        }
    }
};
