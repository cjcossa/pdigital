<?php

namespace App\Interfaces;

use App\DTO\GroupData;

interface GroupRepositoryInterface 
{
    public function getAllGroups();
    public function getGroupById($GroupId);
    public function deleteGroup($GroupId);
    public function createGroup(GroupData $groupData);
    public function updateGroup($GroupId, array $newDetails);
}
