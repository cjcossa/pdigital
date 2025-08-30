<?php

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function getAllUsers();
    public function getUserById($userId);
    public function deleteUser($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function getUsersBygroup($group);
    public function getUsersToSaving();
    public function loginUser(array $userDetails);
    public function logoutUser(Object $token);
}
