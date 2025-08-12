<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'trace_id',
        'role_id',
        'group_cycle_id',
        'status'
    ];
}
