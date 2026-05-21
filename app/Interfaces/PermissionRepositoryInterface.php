<?php

namespace App\Interfaces;

use App\DTO\PermissionData;
interface PermissionRepositoryInterface 
{
    public function createPermission(PermissionData $permissionData);
}
