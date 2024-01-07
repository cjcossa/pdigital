<?php

namespace App\Repositories;

use App\Interfaces\GroupRepositoryInterface;
use App\Models\Group;

class GroupRepository implements GroupRepositoryInterface 
{
    public function getAllGroups() 
    {
        return Group::all();
    }

    public function getGroupById($GroupId) 
    {
        return Group::findOrFail($GroupId);
    }

    public function deleteGroup($GroupId) 
    {
        Group::destroy($GroupId);
    }

    public function createGroup(array $GroupDetails) 
    {
        return Group::create($GroupDetails);
    }

    public function updateGroup($GroupId, array $newDetails) 
    {
        return Group::whereId($GroupId)->update($newDetails);
    }
}
