<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrFail();

        Group::create([
            'id' => Str::uuid(),
            'name' => 'Grupo Teste',
            'join_code' => 'TEST' . rand(100, 999),
            'owner_id' => $owner->id,
            'status' => 1,
            'description' => 'Grupo para testes',
        ]);
    }
}