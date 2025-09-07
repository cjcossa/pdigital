<?php

namespace App\Repositories;

use App\DTO\RoleData;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface 
{
    protected $_roleModel;

    public function __construct(Role $roleModel)
    {
        $this->_roleModel = $roleModel;
    }

    public function createRole(RoleData $roleData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_roleModel->create(
                $roleData->filteredArray()
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
