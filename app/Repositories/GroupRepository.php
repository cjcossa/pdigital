<?php

namespace App\Repositories;

use App\DTO\GroupData;
use App\Interfaces\GroupRepositoryInterface;
use App\Models\Group;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface 
{
    protected $_groupModel;

    public function __construct(Group $groupModel)
    {
        $this->_groupModel = $groupModel;
    }
    public function getAllGroups() 
    {
        return Group::all();
    }

    public function getGroupById($GroupId) 
    {
        return Group::findOrFail($GroupId);
    }

    public function deleteGroup($GroupId) 
    {
        Group::destroy($GroupId);
    }

    public function createGroup(GroupData $groupData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_groupModel->create(
                $groupData->filteredArray()
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

    public function updateGroup($GroupId, array $newDetails) 
    {
        return Group::whereId($GroupId)->update($newDetails);
    }
}
