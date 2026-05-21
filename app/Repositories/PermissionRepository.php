<?php

namespace App\Repositories;

use App\DTO\PermissionData;
use App\Interfaces\PermissionRepositoryInterface;
use App\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface 
{
    protected $_permissionModel;

    public function __construct(Permission $roleModel)
    {
        $this->_permissionModel = $roleModel;
    }

    public function createPermission(PermissionData $permissionData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_permissionModel->create(
                $permissionData->filteredArray()
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
