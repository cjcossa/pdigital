<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'interest_rate_id',
        'group_cycle_id',
        'amount',
        'guarantor_id',
        'status',
        'description'
    ];
}
