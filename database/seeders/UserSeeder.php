<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $userId = (string) Str::uuid();

        User::create([
            'id' => $userId,
            'first_name' => 'João',
            'last_name' => 'Teste',
            'pin' => bcrypt('1234'),
            'primary_phone' => '840000000',
            'status' => Status::ACTIVE->value,
            'trace_id' => $userId,
        ]);
    }
}