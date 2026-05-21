<?php

namespace App\Interfaces;

use App\DTO\RolePermissionData;
use App\Models\Role;

interface RolePermissionRepositoryInterface 
{
    public function createRolePermission(RolePermissionData $rolePermissionData);
}
