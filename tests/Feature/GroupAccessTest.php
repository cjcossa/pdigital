<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class GroupAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_only_sees_and_opens_groups_they_own(): void
    {
        $manager = $this->createUser();
        $assignedGroupOwner = $this->createUser();
        $otherUser = $this->createUser();
        $ownedGroup = $this->createGroup($manager);
        $assignedGroup = $this->createGroup($assignedGroupOwner);
        $otherGroup = $this->createGroup($otherUser);
        $this->assignManagerToGroup($manager, $assignedGroup, $assignedGroupOwner);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/groups')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $ownedGroup->id])
            ->assertJsonFragment(['id' => $assignedGroup->id]);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/groups/{$ownedGroup->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ownedGroup->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/groups/{$assignedGroup->id}/cycles")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.group_id', $assignedGroup->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/groups/{$otherGroup->id}")
            ->assertNotFound();

        $this->actingAs($manager, 'sanctum')
            ->putJson("/api/groups/{$otherGroup->id}", ['name' => 'Alterado'])
            ->assertNotFound();

        $this->actingAs($manager, 'sanctum')
            ->deleteJson("/api/groups/{$otherGroup->id}")
            ->assertNotFound();

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/groups/{$otherGroup->id}/cycles")
            ->assertNotFound();
    }

    public function test_manager_cannot_assign_a_new_group_to_another_user(): void
    {
        $manager = $this->createUser();
        $otherUser = $this->createUser();
        $ownedGroup = $this->createGroup($manager);
        $this->assignManagerToGroup($manager, $ownedGroup, $manager);

        $this->actingAs($manager, 'sanctum')
            ->postJson('/api/groups.create', [
                'gname' => 'Novo grupo',
                'oid' => $otherUser->id,
                'jcod' => 'NEWGRP01',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('groups', [
            'name' => 'Novo grupo',
            'owner_id' => $manager->id,
        ]);
        $this->assertDatabaseMissing('groups', [
            'name' => 'Novo grupo',
            'owner_id' => $otherUser->id,
        ]);
    }

    public function test_admin_can_see_all_groups(): void
    {
        $admin = $this->createUser();
        $this->createUserRole($admin, 'admin');
        $this->createGroup($this->createUser());
        $this->createGroup($this->createUser());

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/groups')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_group_routes_require_authentication(): void
    {
        $this->getJson('/api/groups')->assertUnauthorized();
        $this->getJson('/api/savings')->assertUnauthorized();
        $this->getJson('/api/loans')->assertUnauthorized();
        $this->getJson('/api/payments')->assertUnauthorized();
    }

    public function test_manager_reads_are_scoped_to_their_groups(): void
    {
        $manager = $this->createUser();
        $otherOwner = $this->createUser();
        $managedGroup = $this->createGroup($manager);
        $otherGroup = $this->createGroup($otherOwner);
        $managedCycle = $this->assignManagerToGroup($manager, $managedGroup, $manager);
        $otherCycle = $this->createGroupCycle($otherGroup, $otherOwner);
        $managedMember = $this->createUser();
        $otherMember = $this->createUser();
        $this->assignRoleToGroupMember($managedMember, $managedGroup, $managedCycle, $manager, 'member');
        $this->assignRoleToGroupMember($otherMember, $otherGroup, $otherCycle, $otherOwner, 'member');

        $managedLoan = $this->createLoan($managedMember, $managedGroup, $managedCycle, $manager);
        $this->createLoan($otherMember, $otherGroup, $otherCycle, $otherOwner);
        $this->createSaving($managedMember, $managedGroup, $managedCycle, $manager);
        $this->createSaving($otherMember, $otherGroup, $otherCycle, $otherOwner);
        $this->createPayment($managedLoan, $managedMember, $managedGroup, $managedCycle, $manager);
        $this->createPayment(
            DB::table('loans')->where('group_id', $otherGroup->id)->value('id'),
            $otherMember,
            $otherGroup,
            $otherCycle,
            $otherOwner
        );

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonFragment(['id' => $managedMember->id])
            ->assertJsonMissing(['id' => $otherMember->id]);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/savings')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.group_id', $managedGroup->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/loans')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.group_id', $managedGroup->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/payments')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.group_id', $managedGroup->id);
    }

    public function test_member_reads_only_their_own_records(): void
    {
        $owner = $this->createUser();
        $group = $this->createGroup($owner);
        $cycleId = $this->createGroupCycle($group, $owner);
        $member = $this->createUser();
        $otherMember = $this->createUser();
        $this->assignRoleToGroupMember($member, $group, $cycleId, $owner, 'member');
        $this->assignRoleToGroupMember($otherMember, $group, $cycleId, $owner, 'member');
        $loanId = $this->createLoan($member, $group, $cycleId, $owner);
        $otherLoanId = $this->createLoan($otherMember, $group, $cycleId, $owner);
        $this->createSaving($member, $group, $cycleId, $owner);
        $this->createSaving($otherMember, $group, $cycleId, $owner);
        $this->createPayment($loanId, $member, $group, $cycleId, $owner);
        $this->createPayment($otherLoanId, $otherMember, $group, $cycleId, $owner);

        $this->actingAs($member, 'sanctum')
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonFragment(['id' => $member->id])
            ->assertJsonMissing(['id' => $otherMember->id]);

        $this->actingAs($member, 'sanctum')
            ->getJson('/api/savings')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $member->id);

        $this->actingAs($member, 'sanctum')
            ->getJson('/api/loans')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $member->id);

        $this->actingAs($member, 'sanctum')
            ->getJson('/api/payments')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $member->id);
    }

    private function createUser(): User
    {
        $id = (string) Str::uuid();

        return User::create([
            'id' => $id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'primary_phone' => (string) random_int(100000000, 999999999),
            'pin' => bcrypt('1234'),
            'trace_id' => $id,
            'status' => Status::ACTIVE->value,
        ]);
    }

    private function createGroup(User $owner): Group
    {
        return Group::create([
            'name' => 'Group ' . Str::random(6),
            'join_code' => strtoupper(Str::random(8)),
            'owner_id' => $owner->id,
            'status' => Status::ACTIVE->value,
        ]);
    }

    private function createUserRole(User $user, string $roleName): void
    {
        $role = Role::firstOrCreate(
            ['name' => $roleName],
            [
                'description' => $roleName,
                'status' => Status::ACTIVE->value,
                'trace_id' => $user->id,
            ]
        );

        DB::table('user_roles')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function assignManagerToGroup(User $manager, Group $group, User $traceUser): string
    {
        $role = Role::firstOrCreate(
            ['name' => 'gestor'],
            [
                'description' => 'Gestor do grupo',
                'status' => Status::ACTIVE->value,
                'trace_id' => $traceUser->id,
            ]
        );
        $cycleId = $this->createGroupCycle($group, $traceUser);
        $now = now();

        DB::table('group_users')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $manager->id,
            'group_id' => $group->id,
            'role_id' => $role->id,
            'group_cycle_id' => $cycleId,
            'trace_id' => $traceUser->id,
            'status' => Status::ACTIVE->value,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $cycleId;
    }

    private function createGroupCycle(Group $group, User $traceUser): string
    {
        $cycleId = (string) Str::uuid();
        $now = now();

        DB::table('group_cycles')->insert([
            'id' => $cycleId,
            'group_id' => $group->id,
            'trace_id' => $traceUser->id,
            'contribution_amount' => 0,
            'status' => Status::ACTIVE->value,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $cycleId;
    }

    private function assignRoleToGroupMember(
        User $user,
        Group $group,
        string $cycleId,
        User $traceUser,
        string $roleName
    ): void {
        $role = Role::firstOrCreate(
            ['name' => $roleName],
            [
                'description' => $roleName,
                'status' => Status::ACTIVE->value,
                'trace_id' => $traceUser->id,
            ]
        );
        $now = now();

        DB::table('group_users')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role_id' => $role->id,
            'group_cycle_id' => $cycleId,
            'trace_id' => $traceUser->id,
            'status' => Status::ACTIVE->value,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function createLoan(User $user, Group $group, string $cycleId, User $traceUser): string
    {
        $id = (string) Str::uuid();
        DB::table('loans')->insert([
            'id' => $id,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'group_cycle_id' => $cycleId,
            'interest_rate_id' => (string) Str::uuid(),
            'trace_id' => $traceUser->id,
            'amount' => 100,
            'phone_number' => $user->primary_phone,
            'type' => 'GM',
            'payment_date' => now()->toDateString(),
            'status' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    private function createSaving(User $user, Group $group, string $cycleId, User $traceUser): void
    {
        DB::table('savings')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'wallet_code' => 'WALLET',
            'group_cycle_id' => $cycleId,
            'trace_id' => $traceUser->id,
            'amount' => 10,
            'phone_number' => $user->primary_phone,
            'status' => Status::ACTIVE->value,
            'saving_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPayment(
        string $loanId,
        User $user,
        Group $group,
        string $cycleId,
        User $traceUser
    ): void {
        DB::table('payments')->insert([
            'id' => (string) Str::uuid(),
            'loan_id' => $loanId,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'group_cycle_id' => $cycleId,
            'wallet_code' => 'WALLET',
            'trace_id' => $traceUser->id,
            'phone_number' => $user->primary_phone,
            'amount' => 10,
            'interest' => 0,
            'transaction_reference' => 'TX-' . Str::random(8),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
