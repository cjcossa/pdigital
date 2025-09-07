<?php

namespace App\Interfaces;

use App\DTO\UserData;
use Illuminate\Foundation\Auth\User;

interface UserRepositoryInterface 
{
    public function getAllUsers();
    public function getUserById($userId);
    public function deleteUser($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function getUsersBygroup($group);
    public function getUsersToSaving();
    public function loginUser(UserData $userData);
    public function logoutUser(Object $token);
}
