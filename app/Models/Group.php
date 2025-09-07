<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;
    protected $table = 'groups';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'join_code',
        'owner_id',
        'status',
        'image',
        'description'
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
