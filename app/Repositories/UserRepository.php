<?php

namespace App\Repositories;

use App\Consts\Group;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface 
{
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
            
        }catch(\Exception $e)
        {

        }
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
