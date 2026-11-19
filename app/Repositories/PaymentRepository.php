<?php

namespace App\Repositories;

use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\InterestRate;
use App\Models\Payment;
use App\Repositories\Concerns\ScopesToGroupAccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentRepository implements PaymentRepositoryInterface
{
    use ScopesToGroupAccess;

    private LoanRepositoryInterface $loanRepository;
    private $data;

    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
        $this->data = null;
    }

    public function getAllPayments() 
    {
        $query = Payment::query()
                        ->join('loans', 'payments.loan_id', '=', 'loans.id')
                        ->join('users', 'users.id', '=', 'loans.user_id');
        $this->scopeToAccessibleGroups($query, 'payments.group_id', 'payments.user_id');

        return $query
                        ->get([
                            'payments.*',
                            'loans.amount as loan_amount',
                            \Illuminate\Support\Facades\DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                        ]);
    }

    public function getPaymentById($paymentId) 
    {
        $query = Payment::query();
        $this->scopeToAccessibleGroups($query, 'payments.group_id', 'payments.user_id');

        return $query->findOrFail($paymentId);
    }

    public function deletePayment($paymentId) 
    {
        $this->getPaymentById($paymentId)->delete();
    }

    public function createPayment(array $paymentDetails) 
    {
        if($this->isValidPayment($paymentDetails))
            return Payment::create($paymentDetails);

        return null;
    }


    

    public function updatePayment($paymentId, array $newDetails) 
    {
        return $this->getPaymentById($paymentId)->update($newDetails);
    }
    
    public function getTotalAmountPayments($date = null)
    {
        $query = Payment::query();
        $this->scopeToAccessibleGroups($query, 'payments.group_id', 'payments.user_id');

        if(!empty($date))
        {
            $date = Carbon::parse($date);
            return $query->selectRaw('SUM(amount) AS total')
                            ->selectRaw('SUM(interest) AS interest')
                            ->whereYear('created_at', '=', $date->year)
                            ->whereMonth('created_at', '=', $date->month)
                            ->get(); 
        }

        return $query->selectRaw('SUM(amount) AS total')
                        ->selectRaw('SUM(interest) AS interest')
                        ->get()
                        ->first(); 
    }

    public function getPaymentByLoan($loanId)
    {
        $query = Payment::query();
        $this->scopeToAccessibleGroups($query, 'payments.group_id', 'payments.user_id');

        return $query->where('loan_id', $loanId)
                    ->get();
        
    }
    public function getPaymentByDate($loanId = null, $date)
    {
        $query = Payment::query();
        $this->scopeToAccessibleGroups($query, 'payments.group_id', 'payments.user_id');

        if(!empty($date))
        {
            $date = Carbon::parse($date);
            
            if(checkdate($date->month, $date->day, $date->year) && !empty($loanId))
            {
                return $query->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month)
                            ->where('loan_id', $loanId)
                            ->get();
            }

            if(checkdate($date->month, $date->day, $date->year))
            {
                return $query->whereYear('created_at', '=', $date->year)
                                ->whereMonth('created_at', '=', $date->month)
                                ->get();
            }
        }

        if(!empty($loanId))
        {
            return $this->getPaymentByLoan($loanId);
        }
    }

    public function getPaymentAmounByLoan($loanId)
    {
        $query = Payment::query();
        $this->scopeToAccessibleGroups($query, 'payments.group_id', 'payments.user_id');

        return $query->where('loan_id', $loanId)
                        ->selectRaw('SUM(amount) AS total_paid')
                        ->selectRaw('SUM(interest) AS interest')
                        ->first();                     
    }

    public function getRemaingPaymentByLoan($loanId)
    {
        $this->data = $this->loanRepository->getLoanById($loanId);

        if($this->data)
        {
            $total_paid = (float) ($this->getPaymentAmounByLoan($loanId)->total_paid ?? 0);

            $remaing_payment = $this->data->amount - $total_paid;

            $interest_rate = InterestRate::query()
                ->where('id', $this->data->interest_rate_id)
                ->value('fee');
            if ($interest_rate === null) {
                return null;
            }
            $interest = ($interest_rate * $remaing_payment) / 100;

            return [
                'loan_amount' => $this->data->amount,
                'total_paid' => $total_paid,
                'remaing_payment' => $remaing_payment,
                'type' => $this->data->type,
                'next_interest' => $interest,
                'interest_rate' => $interest_rate,
                'is_paid' => ($total_paid >= $this->data->amount) ? true : false
            ];
        }
    }

    private function isValidPayment(array $paymentDetails)
    {
        $this->data = $this->getRemaingPaymentByLoan($paymentDetails['loan_id']);

        Log::info($this->data);
        if($this->data)
        {
            // $interest_rate = $this->groupRepository->getGroupById($this->data['type'])->interest_rate;

            // $interest = ($interest_rate * $this->data['remaing_payment']) / 100;
            // Log::info($interest_rate);
            // Log::info($interest);


            if($this->data['next_interest'] <= $paymentDetails['interest'])
                return true;
        }

        return false;
    }

}
