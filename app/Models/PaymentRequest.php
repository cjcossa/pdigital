<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'loan_id',
        'user_id',
        'group_id',
        'user_wallet_id',
        'group_cycle_id',
        'trace_id',
        'amount',
        'interest',
        'transaction_reference',
        'description',
        'approved_by'
    ];
}
