<?php

namespace App\Repositories\Concerns;

use App\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopesToGroupAccess
{
    protected function scopeToAccessibleGroups(
        Builder $query,
        string $groupColumn,
        string $userColumn
    ): Builder {
        $user = Auth::user();
        if ($user === null) {
            return $query->whereRaw('1 = 0');
        }

        $groupRepository = app(GroupRepositoryInterface::class);
        if ($groupRepository->isSystemAdmin($user)) {
            return $query;
        }

        $query->whereIn($groupColumn, $groupRepository->getAccessibleGroupIds($user));

        if (! $groupRepository->isGroupManager($user)) {
            $query->where($userColumn, $user->id);
        }

        return $query;
    }
}
