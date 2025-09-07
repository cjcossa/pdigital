<?php

namespace App\Interfaces;

use App\DTO\RoleData;

interface RoleRepositoryInterface 
{
    public function createRole(RoleData $roleData);
}
