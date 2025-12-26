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
    UserSeeder::class,
    RoleSeeder::class,
    GroupSeeder::class,
    GroupCycleSeeder::class,
    InterestModeSeeder::class,   // ← novo
    InterestRateSeeder::class,   // agora seguro
    SavingsSeeder::class,        // ← executado por último
]);
}
}
