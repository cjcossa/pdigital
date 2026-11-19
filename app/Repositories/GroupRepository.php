<?php

namespace App\Repositories;

use App\DTO\GroupData;
use App\Enums\Status;
use App\Interfaces\GroupRepositoryInterface;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface 
{
    protected $_groupModel;

    public function __construct(Group $groupModel)
    {
        $this->_groupModel = $groupModel;
    }
    public function getGroupsForUser(User $user)
    {
        if ($this->hasAdminRole($user)) {
            return Group::all();
        }

        return $this->manageableGroupsQuery($user)->get();
    }

    public function getAccessibleGroupById($groupId, User $user)
    {
        if ($this->hasAdminRole($user)) {
            return Group::findOrFail($groupId);
        }

        return $this->manageableGroupsQuery($user)
            ->where('groups.id', $groupId)
            ->firstOrFail();
    }

    public function getAccessibleGroupIds(User $user): array
    {
        if ($this->isSystemAdmin($user)) {
            return Group::query()->pluck('id')->all();
        }

        if ($this->isGroupManager($user)) {
            return $this->manageableGroupsQuery($user)->pluck('groups.id')->all();
        }

        return DB::table('group_users')
            ->where('user_id', $user->id)
            ->where('status', Status::ACTIVE->value)
            ->distinct()
            ->pluck('group_id')
            ->all();
    }

    public function isSystemAdmin(User $user): bool
    {
        return $this->hasAdminRole($user);
    }

    public function isGroupManager(User $user): bool
    {
        return Group::query()
            ->where('groups.owner_id', $user->id)
            ->orWhereExists(function ($query) use ($user) {
                $query->selectRaw('1')
                    ->from('group_users')
                    ->join('roles', 'roles.id', '=', 'group_users.role_id')
                    ->whereColumn('group_users.group_id', 'groups.id')
                    ->where('group_users.user_id', $user->id)
                    ->where('group_users.status', Status::ACTIVE->value)
                    ->where('roles.name', 'gestor');
            })
            ->exists();
    }

    public function canManageGroup(User $user, $groupId): bool
    {
        if ($this->isSystemAdmin($user)) {
            return Group::whereKey($groupId)->exists();
        }

        return $this->manageableGroupsQuery($user)
            ->where('groups.id', $groupId)
            ->exists();
    }

    public function canManageGroups(User $user): bool
    {
        return $this->hasAdminRole($user)
            || $this->manageableGroupsQuery($user)->exists();
    }

    private function hasAdminRole(User $user): bool
    {
        return DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $user->id)
            ->where('roles.name', 'admin')
            ->exists();
    }

    private function manageableGroupsQuery(User $user): Builder
    {
        return Group::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('groups.owner_id', $user->id)
                    ->orWhereExists(function ($subquery) use ($user) {
                        $subquery->selectRaw('1')
                            ->from('group_users')
                            ->join('roles', 'roles.id', '=', 'group_users.role_id')
                            ->whereColumn('group_users.group_id', 'groups.id')
                            ->where('group_users.user_id', $user->id)
                            ->where('group_users.status', Status::ACTIVE->value)
                            ->where('roles.name', 'gestor');
                    });
            });
    }

    public function deleteGroup($GroupId) 
    {
        Group::destroy($GroupId);
    }

    public function createGroup(GroupData $groupData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_groupModel->create(
                $groupData->filteredArray()
            ));

            parent::commit();
            parent::setStatus(true);
            parent::setMessage(__('messages.register_success'));

        }catch(\Exception $e)
        {
            parent::setStatus(false);
            parent::setMessage(__('messages.register_failed').' '.$e->getMessage());
        }
        
        return parent::sendResponse();
    }

    public function updateGroup($GroupId, array $newDetails) 
    {
        return Group::whereId($GroupId)->update($newDetails);
    }
}
