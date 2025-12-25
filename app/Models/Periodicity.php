<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodicity extends Model
{
    protected $table = 'periodicities';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'label',
        'days',
    ];
}
