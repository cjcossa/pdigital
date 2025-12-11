<?php

namespace App\Repositories;

use App\DTO\UserJoinData;
use App\Enums\Status;
use App\Interfaces\GroupUserRepositoryInterface;
use App\Interfaces\UserJoinRequestRepositoryInterface;
use App\Models\GroupUser;
use Carbon\Carbon;

class GroupUserRepository extends BaseRepository implements GroupUserRepositoryInterface
{
    private UserJoinRequestRepositoryInterface $_groupUserRepository;
    protected $_groupUserModel;
    public function __construct(
        UserJoinRequestRepositoryInterface $groupUserRepository,
        GroupUser $groupUserModel)
    {
        $this->_groupUserRepository = $groupUserRepository;
        $this->_groupUserModel = $groupUserModel;
    }
    public function createGroupUser() 
    {
        $processed = 0;
        $error = 0;
        parent::setData(
            $this->_groupUserRepository->getUserJoin(
                        UserJoinData::fromArray([
                        'stus' => Status::APPROVED->value
                ]))->data
        );

        if(parent::isData())
        {
            foreach(parent::getData() as $userJoinData)
            {
                try
                {
                    $this->_groupUserModel->create([
                        'id' => $userJoinData->id,
                        'user_id' => $userJoinData->user_id,
                        'group_id' => $userJoinData->group_id,
                        'role_id' => $userJoinData->role_id,
                        'group_cycle_id' => $userJoinData->group_cycle_id,
                        'trace_id' => $userJoinData->trace_id,
                        'status' => Status::ACTIVE->value,
                        'created_at' => Carbon::now()
                    ]);

                    $this->_groupUserRepository->updateUserJoin(
                        UserJoinData::fromArray([
                        'stus' => Status::PROCESSED->value,
                        'id' => $userJoinData->id,
                    ]));

                    $processed++;

                }catch(\Exception $e)
                {
                    parent::setMessage(__('messages.register_failed'). $e->getMessage());
                    parent::log();

                    $this->_groupUserRepository->updateUserJoin(
                        UserJoinData::fromArray([
                        'stus' => Status::ERROR->value,
                        'id' => $userJoinData->id,
                    ]));

                    $error++;
                    continue;
                }
            }
            
        }

        parent::setStatus(true);
        parent::setData([
            'processed' => $processed,
            'error' => $error
        ]);

        return parent::sendResponse();
    }    
}
