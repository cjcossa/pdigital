<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestRate extends Model
{
    use HasFactory;
    protected $fillable = [
        'interest_mode_id',
        'group_id',
        'trace_id',
        'group_id',
        'fee',
        'group_cycle_id',
        'description',
        'status'
    ];
}
