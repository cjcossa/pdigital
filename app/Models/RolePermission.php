<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;


class RolePermission extends Model
{
    use HasFactory;
    protected $table = 'role_permissions';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id',
        'status',
        'trace_id'
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
