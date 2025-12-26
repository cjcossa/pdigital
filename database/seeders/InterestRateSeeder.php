<?php

namespace Database\Seeders;

use App\Models\InterestRate;
use App\Models\Group;
use App\Models\GroupCycle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class InterestRateSeeder extends Seeder
{
    public function run(): void
    {
        $role = \App\Models\Role::firstOrFail(); // pega algum role existente
        $group = Group::firstOrFail();
        $cycle = GroupCycle::firstOrFail();
        $user = User::firstOrFail(); // pega o primeiro usuário do banco

        InterestRate::create([
            'id' => Str::uuid(),
            'interest_mode_id' => Str::uuid(), 
            'role_id' => $role->id,                 
            'group_id' => $group->id,
            'group_cycle_id' => $cycle->id,
            'fee' => 10,
            'status' => 1,
            'description' => 'Juro padrão',
            'trace_id' => $user->id,           
        ]);
    }
}