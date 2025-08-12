<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupJoinRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'trace_id',
        'approved_by',
        'service',
        'group_cycle_id',
        'phone_number',
        'mensagem',
        'status'
    ];
}
