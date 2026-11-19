<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $phone = trim((string) env('ADMIN_USER_PHONE', ''));
        $pin = (string) env('ADMIN_USER_PIN', '');

        if ($phone === '' || $pin === '') {
            throw new RuntimeException(
                'Configure ADMIN_USER_PHONE e ADMIN_USER_PIN no .env antes de executar os seeders.'
            );
        }

        if (! preg_match('/^\d{9}$/D', $phone)) {
            throw new RuntimeException('ADMIN_USER_PHONE deve conter exactamente 9 dígitos.');
        }

        if (! preg_match('/^\d{4,12}$/D', $pin)) {
            throw new RuntimeException('ADMIN_USER_PIN deve conter entre 4 e 12 dígitos.');
        }

        $user = User::firstOrNew(['primary_phone' => $phone]);
        if (! $user->exists) {
            $user->id = (string) Str::uuid();
            $user->trace_id = $user->id;
        }

        $user->first_name = 'Administrador';
        $user->last_name = 'Sistema';
        $user->pin = Hash::make($pin);
        $user->status = Status::ACTIVE->value;
        $user->created_at ??= now();
        $user->save();

        $adminRole = Role::query()->where('name', 'admin')->firstOrFail();
        $now = now();

        DB::table('user_roles')->insertOrIgnore([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
