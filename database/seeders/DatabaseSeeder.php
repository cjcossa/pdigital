<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        if (trim((string) env('ADMIN_USER_PHONE', '')) === '' || (string) env('ADMIN_USER_PIN', '') === '') {
            throw new RuntimeException(
                'Configure ADMIN_USER_PHONE e ADMIN_USER_PIN no .env antes de executar os seeders.'
            );
        }

        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            GroupSeeder::class,
            PeriodicitiesSeeder::class,
            GroupCycleSeeder::class,
            InterestModeSeeder::class,   // ← novo
            InterestRateSeeder::class,   // agora seguro
            SavingsSeeder::class,        // ← executado por último
            
        ]);
    }

}
