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
        $user = User::firstOrNew([
            'primary_phone' => '840000000',
        ]);

        if (! $user->exists) {
            $user->id = (string) Str::uuid();
            $user->trace_id = $user->id;
        }

        $user->first_name = 'João';
        $user->last_name = 'Teste';
        $user->pin = bcrypt('1234');
        $user->status = Status::ACTIVE->value;
        $user->save();
    }
}