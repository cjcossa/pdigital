<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Group;
use App\Models\GroupCycle;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberUserSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('primary_phone', '840000000')->firstOrFail();
        $group = Group::firstOrFail();
        $cycle = GroupCycle::where('group_id', $group->id)->firstOrFail();
        $role = Role::where('name', 'member')->firstOrFail();

        $member = User::firstOrNew(['primary_phone' => '860000000']);
        if (! $member->exists) {
            $member->id = (string) Str::uuid();
            $member->trace_id = $owner->id;
        }

        $member->first_name = 'Membro';
        $member->last_name = 'Teste';
        $member->pin = Hash::make('1234');
        $member->status = Status::ACTIVE->value;
        $member->save();

        DB::table('group_users')->updateOrInsert(
            [
                'user_id' => $member->id,
                'group_id' => $group->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $role->id,
                'group_cycle_id' => $cycle->id,
                'trace_id' => $owner->id,
                'status' => Status::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
}
