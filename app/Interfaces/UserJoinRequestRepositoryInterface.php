<?php

namespace App\Interfaces;

use App\DTO\UserJoinData;

interface UserJoinRequestRepositoryInterface 
{
    public function createUserJoin(UserJoinData $userJoinData);
    public function updateUserJoin(UserJoinData $userJoinData);
    public function getUserJoin(UserJoinData $userJoinData);

}
