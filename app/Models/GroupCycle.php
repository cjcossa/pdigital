<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCycle extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'group_id',
        'trace_id',
        'start_date',
        'end_date',
        'status'
    ];
}
