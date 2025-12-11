<?php

namespace App\Repositories;

use App\DTO\GroupCycleData;
use App\DTO\UserJoinData;
use App\Interfaces\UserJoinRequestRepositoryInterface;
use App\Models\GroupCycle;
use App\Models\UserJoinRequest;

class UserJoinRequestRepository extends BaseRepository implements UserJoinRequestRepositoryInterface 
{
    protected $_userJoinRequestModel;

    public function __construct(UserJoinRequest $userJoinRequestModel)
    {
        $this->_userJoinRequestModel = $userJoinRequestModel;
    }

    public function createUserJoin(UserJoinData $userJoinData) 
    {
        try
        {
            parent::begin();

            parent::setData($this->_userJoinRequestModel->create(
                $userJoinData->filteredArray()
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

    public function updateUserJoin(UserJoinData $userJoinData)
    {
        try
        {

            parent::setData();

            //$this->_data ['updated_at'] = Carbon::now();
            parent::setData(
                $this->_userJoinRequestModel->where('id', $userJoinData->id)
                                                ->update($userJoinData->filteredArray())
            );   
            parent::commit();
            parent::setStatus(true);                              
            
        }catch(\Exception $e)
        {
            parent::setStatus(false);
            parent::setMessage(__('messages.register_failed').' '.$e->getMessage());
        }

        return parent::sendResponse();

    }

    public function getUserJoin(UserJoinData $userJoinData)
    {
        try
        {
            if($userJoinData->id)
                $this->getUserJoinRequestById($userJoinData->id);
            else if ($userJoinData->user_id)
                $this->getUserJoinRequestByUser($userJoinData->user_id);
            else if($userJoinData->status)
                $this->getUserJoinRequestByStatus($userJoinData->status);                               

        }catch(\Exception $e)
        {
            parent::setStatus(false);
            parent::setMessage(__('messages.register_failed').' '.$e->getMessage());
        }

        return parent::sendResponse();
    }

    
    private function getUserJoinRequestById($id)
    {
        parent::setData(
            $this->_userJoinRequestModel->where('id', $id)
                                            ->first()
        );     
    }

    private function getUserJoinRequestByUser($user_id)
    {
        parent::setData(
            $this->_userJoinRequestModel->where('user_id', operator: $user_id)
                                            ->first()
        );     
    }

    private function getUserJoinRequestByStatus($status, $records = 10)
    {
        parent::setData($this->_userJoinRequestModel->where('status', $status)
                                            ->orderBy('updated_at', 'asc')
                                            ->take($records)
                                            ->get()
        );
    }
}
