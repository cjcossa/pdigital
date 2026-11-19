<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GroupCycle extends Model
{
    use HasFactory;
    protected $table = 'group_cycles';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'group_id',
        'trace_id',
        'start_date',
        'end_date',
        'contribution_amount',
        'status'
    ];
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            $model->created_at = Carbon::now();
        });
    }
}
