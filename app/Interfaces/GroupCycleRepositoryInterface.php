<?php

namespace App\Interfaces;

use App\DTO\GroupCycleData;

interface GroupCycleRepositoryInterface 
{
    public function getGroupCycles(string $groupId);
    public function createGroupCycle(GroupCycleData $groupCycleData);
}
