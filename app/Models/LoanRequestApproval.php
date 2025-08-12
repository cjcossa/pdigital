<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRequestApproval extends Model
{
    use HasFactory;
    protected $fillable = [
        'loan_request_id',
        'approved_by'
    ];
}
