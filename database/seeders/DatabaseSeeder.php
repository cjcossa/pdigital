<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
<<<<<<< HEAD
    public function run(): void
    {
        $this->call(PeriodicitiesSeeder::class);

    }
=======
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
>>>>>>> 55a5fa99418be6e6f285f60b59ae84510e1bab3b
}
