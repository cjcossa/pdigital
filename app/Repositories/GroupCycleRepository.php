<?php

namespace App\Repositories;

use App\DTO\GroupCycleData;
use App\Interfaces\GroupCycleRepositoryInterface;
use App\Models\GroupCycle;

class GroupCycleRepository extends BaseRepository implements GroupCycleRepositoryInterface 
{
    protected $_groupCycleModel;

    public function __construct(GroupCycle $groupCycleModel)
    {
        $this->_groupCycleModel = $groupCycleModel;
    }

    public function getGroupCycles(string $groupId)
    {
        return $this->_groupCycleModel->where('group_id', $groupId)->get();
    }

    public function createGroupCycle(GroupCycleData $groupCycleData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_groupCycleModel->create(
                $groupCycleData->filteredArray()
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
