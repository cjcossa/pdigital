<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Saving;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupCycle;
use Illuminate\Support\Str;

class SavingsSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar primeiros registros das tabelas relacionadas
        $user = User::firstOrFail();
        $group = Group::firstOrFail();
        $groupCycle = GroupCycle::firstOrFail();

        Saving::updateOrCreate(
            [
                'user_id' => $user->id,
                'wallet_code' => 'TEST_WALLET_001', // pode ser qualquer código único
            ],
            [
                'id' => Str::uuid(),
                'group_id' => $group->id,
                'group_cycle_id' => $groupCycle->id,
                'trace_id' => $user->id, // ou outro ID se necessário
                'amount' => 1000000000, // 1B
                'phone_number' => '840000000',
                'status' => 'active',
                'social_amount' => 0,
                'transaction_reference' => 'TEST_REF_001',
                'description' => 'Empréstimo inicial do grupo',
            ]
        );
    }
}