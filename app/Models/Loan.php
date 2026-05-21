<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'user_id',
    //     'group_id',
    //     'interest_rate_id',
    //     'group_cycle_id',
    //     'amount',
    //     'guarantor_id',
    //     'status',
    //     'description'
    // ];

    protected $fillable = [
        'user_id',
        'user_request_id',
        'group_id',
        'group_cycle_id',
        'interest_rate_id',
        'guarantor_id',
        'trace_id',
        'amount',
        'phone_number',
        'type',
        'payment_date',
        'status',
        'transaction_reference',
        'description'
    ];


    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->id)) {
            $model->id = (string) Str::uuid();
        }
        if (empty($model->created_at)) {
            $model->created_at = Carbon::now();
        }
    });
}
   
}