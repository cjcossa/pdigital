<?php

namespace App\Repositories;

use App\DTO\PreUserData;
use App\DTO\UserData;
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
use App\Interfaces\GroupRepositoryInterface;

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
        $query = User::query()
                    ->leftJoin('group_users', 'group_users.user_id', '=', 'users.id')
                    ->leftJoin('groups', 'groups.id', '=', 'group_users.group_id');
        $this->scopeUsersToCurrentUser($query);

        return $query->get([
                        'users.*',
                        'group_users.group_id as group_id',
                        'groups.name as group_name',
                    ]);
    }

    public function getUserById($UserId) 
    {
        $query = User::query()
                    ->leftJoin('group_users', 'group_users.user_id', '=', 'users.id')
                    ->leftJoin('groups', 'groups.id', '=', 'group_users.group_id')
                    ->where('users.id', $UserId);
        $this->scopeUsersToCurrentUser($query);

        return $query->firstOrFail([
                        'users.*',
                        'group_users.group_id as group_id',
                        'groups.name as group_name',
                    ]);
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
        $user = Auth::user();
        $query = User::query()
            ->join('group_users', 'group_users.user_id', '=', 'users.id')
            ->where('group_users.group_id', $group);

        if ($user === null) {
            $query->whereRaw('1 = 0');
        } elseif (! app(GroupRepositoryInterface::class)->isSystemAdmin($user)
            && ! app(GroupRepositoryInterface::class)->canManageGroup($user, $group)) {
            $query->where('group_users.user_id', $user->id);
        }

        return $query->get(['users.*']);
    }
    public function getUsersToSaving()
    {
        $user = Auth::user();
        $query = User::query()
                    ->join('group_users', 'group_users.user_id', '=', 'users.id')
                    ->where('group_users.status', Status::ACTIVE->value);

        if ($user === null) {
            $query->whereRaw('1 = 0');
        } elseif (app(GroupRepositoryInterface::class)->isSystemAdmin($user)) {
            // Administrators can select members from any group.
        } elseif (app(GroupRepositoryInterface::class)->isGroupManager($user)) {
            $query->whereIn(
                'group_users.group_id',
                app(GroupRepositoryInterface::class)->getAccessibleGroupIds($user)
            );
        } else {
            $query->where('users.id', $user->id);
        }

        return $query->distinct()->get(['users.*']);
    }

    public function loginUser(UserData $userData)
    {
        try
        {
            $user = $this->_userModel
                ->where('primary_phone', $userData->primaryPhone)
                ->first();

            if ($user && Hash::check($userData->pin, $user->pin)) {
                parent::setMessage(__('messages.user_loggedin'));
                parent::setStatus(true);
                parent::setData($this->getAccessToken(
                    $this->createAccessToken($user->primary_phone)
                ));
            } else {
                parent::setMessage(__('auth.failed'));
                parent::setStatus(false);
                parent::setData(null);
            }
        }catch(\Exception $e)
        {
            parent::setMessage(__('messages.register_failed'). $e->getMessage());
            parent::log();
        }

       return parent::sendResponse();
    }
    public function logoutUser(Object $token)
    {
        //Log::info($token->user()->currentAccessToken());
        $this->deleteAccessToken($token->user()->currentAccessToken());
        
        return Auth::check();
    }

    public function createAccessToken(string $username): NewAccessToken
    {
        $user = User::query()
                    ->where("primary_phone", $username)
                    ->first();

        $token = $user->createToken("auth_token");
        $accessToken = $user->tokens()->latest()->first();
        $accessToken->expires_at = Carbon::now()->addMinutes(5);
        $accessToken->save();

        return $token;
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
        $user = $token->accessToken->tokenable;

        return [
            'primary_phone' => $user->primary_phone,
            'name' => $user->first_name,
            'token' => $token->plainTextToken,
            'id' => $user->id,
            'role' => $this->getUserRole($user),
        ];
    }

    private function getUserRole(User $user): string
    {
        $isAdmin = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $user->id)
            ->where('roles.name', 'admin')
            ->exists();

        if ($isAdmin) {
            return 'admin';
        }

        $isManager = DB::table('group_users')
            ->join('roles', 'roles.id', '=', 'group_users.role_id')
            ->where('group_users.user_id', $user->id)
            ->where('group_users.status', Status::ACTIVE->value)
            ->where('roles.name', 'gestor')
            ->exists()
            || DB::table('groups')
                ->where('owner_id', $user->id)
                ->exists();

        return $isManager ? 'gestor' : 'member';
    }

    private function scopeUsersToCurrentUser($query): void
    {
        $user = Auth::user();
        if ($user === null) {
            $query->whereRaw('1 = 0');
            return;
        }

        $groupRepository = app(GroupRepositoryInterface::class);
        if ($groupRepository->isSystemAdmin($user)) {
            return;
        }

        if ($groupRepository->isGroupManager($user)) {
            $query->whereIn(
                'group_users.group_id',
                $groupRepository->getAccessibleGroupIds($user)
            );
            return;
        }

        $query->where('users.id', $user->id);
    }
    
}
