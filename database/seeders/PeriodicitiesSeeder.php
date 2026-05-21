<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeriodicitiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('periodicities')->insert([
            [
                'id' => Str::uuid(),
                'code' => 'daily',
                'label' => 'Diário',
                'days' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'code' => 'weekly',
                'label' => 'Semanal',
                'days' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'code' => 'monthly',
                'label' => 'Mensal',
                'days' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

