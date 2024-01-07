<?php

namespace App\Interfaces;

interface GroupRepositoryInterface 
{
    public function getAllGroups();
    public function getGroupById($GroupId);
    public function deleteGroup($GroupId);
    public function createGroup(array $GroupDetails);
    public function updateGroup($GroupId, array $newDetails);
}
