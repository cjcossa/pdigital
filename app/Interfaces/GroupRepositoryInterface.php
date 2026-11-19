<?php

namespace App\Interfaces;

use App\DTO\GroupData;
use App\Models\User;

interface GroupRepositoryInterface 
{
    public function getGroupsForUser(User $user);
    public function getAccessibleGroupById($groupId, User $user);
    public function getAccessibleGroupIds(User $user): array;
    public function isSystemAdmin(User $user): bool;
    public function isGroupManager(User $user): bool;
    public function canManageGroup(User $user, $groupId): bool;
    public function canManageGroups(User $user): bool;
    public function deleteGroup($GroupId);
    public function createGroup(GroupData $groupData);
    public function updateGroup($GroupId, array $newDetails);
}
