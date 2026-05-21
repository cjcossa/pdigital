<?php

namespace App\Repositories;

use App\DTO\RolePermissionData;
use App\Interfaces\RolePermissionRepositoryInterface;
use App\Models\RolePermission;

class RolePermissionRepository extends BaseRepository implements RolePermissionRepositoryInterface 
{
    protected $_rolePermissionModel;

    public function __construct(RolePermission $rolePermission)
    {
        $this->_rolePermissionModel = $rolePermission;
    }

    public function createRolePermission(RolePermissionData $rolePermissionData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_rolePermissionModel->create(
                $rolePermissionData->filteredArray()
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
}
