<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'user_wallet_id',
        'group_cycle_id',
        'trace_id',
        'amount',
        'social_amount',
        'transaction_reference',
        'description'
    ];
}
