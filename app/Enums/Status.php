<?php

namespace App\Enums;

enum Status: string
{ 
    case ERROR = 'Erro';
    case ACTIVE = 'Activo';
    case INACTIVE = 'Inactivo';
    case PENDING = 'Pendente';
    case CONFIRMED = 'Confirmado';
    case MIGRATED = 'Migrado';
    case APPROVED = 'Aprovado';
    case PROCESSED = 'Processado';

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


