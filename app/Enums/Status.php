<?php

namespace App\Enums;

enum Status: int
{ 
    case ACTIVE = 0;
    case INACTIVE = 1;
    case P_USER_PENDING = 2;
    case P_USER_CONFIRMED = 3;
    case P_USER_MIGRATED = 4;
}


