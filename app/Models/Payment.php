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
        'wallet_code',
        'group_cycle_id',
        'trace_id',
        'phone_number',
        'amount',
        'interest',
        'transaction_reference',
        'description',
        'created_at'
    ];
}
