<?php

namespace App\Interfaces;

use App\DTO\GroupCycleData;

interface GroupCycleRepositoryInterface 
{
    public function createGroupCycle(GroupCycleData $groupCycleData);
}
