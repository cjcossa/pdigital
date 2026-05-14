<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Group;
use App\Models\User;
use App\Models\GroupCycle;
use App\Models\Periodicity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupCycleSeeder extends Seeder
{
    public function run(): void
    {
        $group = Group::firstOrFail();
        $user = User::firstOrFail(); // pega o usuário já criado
        $periodicity = Periodicity::firstOrFail(); // pega a periodicidade já criada

        GroupCycle::create([
            'id' => Str::uuid(),
            'name' => 'Ciclo 2025',
            'group_id' => $group->id,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'contribution_amount' => 150.00,
            'status' => Status::ACTIVE->value,
            'trace_id' => $user->id, // aponta para o usuário existente
            'periodicity_id' => $periodicity->id,
        ]);
    }
}