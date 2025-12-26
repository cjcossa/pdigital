<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'first_name' => 'João',
            'last_name' => 'Teste',
            'pin' => bcrypt('1234'),
            'primary_phone' => '840000000',
            'status' => 1,
            'trace_id' => Str::uuid(),
        ]);
    }
}