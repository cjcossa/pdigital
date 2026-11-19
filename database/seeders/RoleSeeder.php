<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que existe pelo menos um utilizador
        $user = User::firstOrFail();

        Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'description' => 'Administrador do sistema',
                'status' => 'active',
                'trace_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        Role::firstOrCreate(
            ['name' => 'member'],
            [
                'description' => 'Membro padrão do grupo',
                'status' => 'active',
                'trace_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        Role::firstOrCreate(
            ['name' => 'gestor'],
            [
                'description' => 'Gestor do grupo',
                'status' => 'active',
                'trace_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}