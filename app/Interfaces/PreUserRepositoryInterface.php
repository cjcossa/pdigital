<?php

namespace App\Interfaces;

use App\Models\PreUser;
use App\DTO\PreUserData;

interface PreUserRepositoryInterface 
{
    // public function getAllPreUsers();
    public function getPreUser(PreUserData $preUserData);
    public function createPreUser(PreUserData $preUserData);
    public function updatePreUser(PreUserData $preUserData);
}
