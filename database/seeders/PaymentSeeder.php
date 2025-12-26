<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Loan;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $loan = Loan::first();

        Payment::create([
            'loan_id' => $loan->id,
            'user_id' => $loan->user_id,
            'group_id' => $loan->group_id,
            'group_cycle_id' => $loan->group_cycle_id,
            'amount' => 3000,
            'interest' => 300,
            'description' => 'Primeiro pagamento',
            'trace_id' => 'seed'
        ]);
    }
}