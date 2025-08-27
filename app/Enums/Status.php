<?php

namespace App\Enums;

enum Status: int
{ 
    case ERROR = -1;
    case ACTIVE = 0;
    case INACTIVE = 1;
    case PENDING = 2;
    case CONFIRMED = 3;
    case MIGRATED = 4;

    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Inactivo',
            self::PENDING => 'Pendente',
            self::CONFIRMED => 'Confirmado',
            self::MIGRATED => 'Migrado'
        };
    }
}


