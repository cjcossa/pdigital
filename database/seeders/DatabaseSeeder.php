<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run(): void
{
    $this->call([
        UserSeeder::class,   // ← trace_id depende disto
        RoleSeeder::class,   // ← agora existe role
        GroupSeeder::class,
        GroupCycleSeeder::class,
        InterestRateSeeder::class,
    ]);
}
}
