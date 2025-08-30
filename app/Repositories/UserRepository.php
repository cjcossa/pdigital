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
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $migrated = 0;
        $error = 0;

        parent::setData(
            $this->_preUserRepository->getPreUser(
                        PreUserData::fromArray([
                        'status' => Status::CONFIRMED->value
                ]))->data
        );

        if(parent::isData())
        {
            foreach(parent::getData() as $preUserData)
            {
                try
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
                        'id' => $preUserData->id,
                    ]));

                    $migrated++;

                }catch(\Exception $e)
                {
                    parent::setMessage(__('messages.register_failed'). $e->getMessage());
                    parent::log();

                    $this->_preUserRepository->updatePreUser(
                        PreUserData::fromArray([
                        'status' => Status::ERROR->value,
                        'id' => $preUserData->id,
                    ]));

                    $error++;
                    continue;
                }
            }
            
        }

        parent::setStatus(true);
        parent::setData([
            'migrated' => $migrated,
            'error' => $error
        ]);

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

    public function loginUser(array $userDetails)
    {
        $userDetails['pin'] = Hash::make($userDetails['pin']);
        if(Auth::attempt($userDetails))
        {
            $user = Auth::user();
           
            $token = $this->createAccessToken($user->primary_phone);
            
            return $this->getAccessToken($token);
        }

        return $userDetails;
    }
    public function logoutUser(Object $token)
    {
        Log::info($token->user()->currentAccessToken());
        $this->deleteAccessToken($token->user()->currentAccessToken());
        
        return Auth::check();
    }

    public function createAccessToken(string $username): NewAccessToken
    {
        $user = User::query()
                    ->where("primary_phone", $username)
                    ->first();

        return $user->createToken("auth_token");
    }

    public function deleteAccessToken(PersonalAccessToken $token)
    {
        $token->delete();
    }
    public function deleteAllAccessTokens($tokens)
    {
        foreach ($tokens as $token) {
            $token->delete();
        }
    }

    public function getAccessToken($token)
    {
        return [
            'username' => $token->accessToken->tokenable->primary_phone,
            'name' => $token->accessToken->tokenable->first_name,
            'token' => $token->plainTextToken,
            'group' => $token->accessToken->tokenable->group,
            'id' => $token->accessToken->tokenable->id,
            //'permissions' => $this->groupRepository->getGroupById($token->accessToken->tokenable->group)->permissions
        ];
    }
}
