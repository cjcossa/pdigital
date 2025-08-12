<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUserProfit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'user_wallet_id',
        'group_cycle_id',
        'trace_id',
        'savings_amount',
        'social_amount',
        'interest_amount',
        'transaction_reference',
        'description',
        'calculated_at',
        'status'
    ];
}
