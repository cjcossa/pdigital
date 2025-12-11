<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class GroupUser extends Model
{
    use HasFactory;
    protected $table = 'user_join_requests';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'group_id',
        'trace_id',
        'role_id',
        'group_cycle_id',
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
