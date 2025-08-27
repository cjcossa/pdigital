<?php

namespace App\Repositories;

use App\Consts\Group;
use App\DTO\PreUserData;
use App\Enums\Status;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Interfaces\PreUserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class UserRepository extends BaseRepository implements UserRepositoryInterface 
{
    protected $_data;
    private PreUserRepositoryInterface $_preUserRepository;
    protected $_userModel;
    public function __construct(PreUserRepositoryInterface $preUserRepository,
                                User $userModel)
    {
        $this->_preUserRepository = $preUserRepository;
        $this->_userModel = $userModel;
    }
    public function getAllUsers() 
    {
        return User::join('groups', 'users.group', '=', 'groups.id')
                    ->get(['users.*', 'groups.name as group_name']);
    }

    public function getUserById($UserId) 
    {
        return User::findOrFail($UserId);
    }

    public function deleteUser($UserId) 
    {
        User::destroy($UserId);
    }

    public function createUser(array $userDetails) 
    {
        try
        {
            parent::begin();

            parent::setData(
                $this->_preUserRepository->getPreUser(
                            PreUserData::fromArray([
                            'status' => Status::CONFIRMED->value
                    ]))->data
            );

            //parent::setStatus(false);

            if(parent::isData())
            {
                foreach(parent::getData() as $preUserData)
                {
                    $this->_userModel->create([
                        'id' => $preUserData->id,
                        'first_name' => $preUserData->first_name,
                        'last_name' => $preUserData->last_name,
                        'pin' => $preUserData->pin,
                        'primary_phone' => $preUserData->primary_phone,
                        'phones' => $preUserData->phones,
                        'doc_details' => $preUserData->doc_details,
                        'beneficiaries' => $preUserData->beneficiaries,
                        'trace_id' => $preUserData->id,
                        'status' => Status::ACTIVE->value,
                        'created_at' => Carbon::now()
                    ]);
                    $this->_preUserRepository->updatePreUser(
                        PreUserData::fromArray([
                        'status' => Status::MIGRATED->value,
                        'id' => $preUserData->id
                    ]));
                    parent::commit();
                }
                
                parent::setStatus(true);
            }

        }catch(\Exception $e)
        {
            parent::setMessage(__('messages.register_failed'). $e->getMessage());
        }

        return parent::sendResponse();
    }

    public function updateUser($userId, array $newDetails) 
    {
        return User::whereId($userId)->update($newDetails);
    }

    public function getUsersByGroup($group)
    {
        return User::where('group', $group)
                    ->get();
    }
    public function getUsersToSaving()
    {
        return User::where('group', '<>', Group::G_ETUSR)
                    ->get();
    }
}
