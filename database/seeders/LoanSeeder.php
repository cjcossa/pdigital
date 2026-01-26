<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupCycle;
use App\Models\InterestRate;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        Loan::create([
            'user_id' => User::first()->id,
            'group_id' => Group::first()->id,
            'interest_rate_id' => InterestRate::first()->id,
            'group_cycle_id' => GroupCycle::first()->id,
            'amount' => 10000,
            'status' => 1, // ativo
            'description' => 'Empréstimo inicial'
        ]);
    }
}