<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Saving extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'group_id',
        'wallet_code',
        'group_cycle_id',
        'trace_id',
        'amount',
        'phone_number',
        'status',
        'saving_date',
        'social_amount',
        'transaction_reference',
        'description'
    ];

    protected $casts = [
        'saving_date' => 'date',
        'amount' => 'decimal:2',
        'social_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
