<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InterestMode;
use App\Models\User;

class InterestModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega o primeiro usuário para usar como trace_id
        $userId = User::first()->id;

        // UUID que o InterestRateSeeder vai usar
        $modeId = 'e2d6e1fe-5209-4e2f-8b13-f084ca45b48e';

        // Cria ou garante que existe
        InterestMode::firstOrCreate(
            ['id' => $modeId],
            [
                'code' => 'FIXED',
                'name' => 'Juro Fixo',
                'description' => 'Modo de juros fixo',
                'status' => 'active',
                'trace_id' => $userId,
                'trace_update_id' => $userId,
            ]
        );
    }
}