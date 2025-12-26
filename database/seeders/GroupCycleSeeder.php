<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupCycle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupCycleSeeder extends Seeder
{
    public function run(): void
    {
        $group = Group::firstOrFail();
        $user = User::firstOrFail(); // pega o usuário já criado

        GroupCycle::create([
            'id' => Str::uuid(),
            'name' => 'Ciclo 2025',
            'group_id' => $group->id,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'status' => 1,
            'trace_id' => $user->id, // aponta para o usuário existente
        ]);
    }
}