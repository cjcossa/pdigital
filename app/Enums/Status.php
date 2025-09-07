<?php

namespace App\Enums;

enum Status: string
{ 
    case ERROR = 'ERRO';
    case ACTIVE = 'Activo';
    case INACTIVE = 'Inactivo';
    case PENDING = 'Pendente';
    case CONFIRMED = 'Confirmado';
    case MIGRATED = 'Migrado';

    // public function description(): string
    // {
    //     return match($this) {
    //         self::ACTIVE => 'Activo',
    //         self::INACTIVE => 'Inactivo',
    //         self::PENDING => 'Pendente',
    //         self::CONFIRMED => 'Confirmado',
    //         self::MIGRATED => 'Migrado'
    //     };
    // }
}


