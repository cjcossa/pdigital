<?php

namespace App\Repositories;

use App\Consts\State;
use App\Interfaces\GroupRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class PaymentRepository implements PaymentRepositoryInterface
{
    private LoanRepositoryInterface $loanRepository;
    private GroupRepositoryInterface $groupRepository;
    private $data;

    public function __construct(LoanRepositoryInterface $loanRepository, 
                                GroupRepositoryInterface $groupRepository) 
    {
        $this->loanRepository = $loanRepository;
        $this->groupRepository = $groupRepository;
        $this->data = null;
    }

    public function getAllPayments() 
    {
        return Payment::join('loans', 'payments.loan_id', '=', 'loans.id')
                        ->join('users', 'users.id', '=', 'loans.user_id')
                        ->get(['payments.*', 'loans.amount as loan_amount', 'users.name']);
    }

    public function getPaymentById($paymentId) 
    {
        return Payment::findOrFail($paymentId);
    }

    public function deletePayment($paymentId) 
    {
        Payment::destroy($paymentId);
    }

    public function createPayment(array $paymentDetails) 
    {
        if($this->isValidPayment($paymentDetails))
            return Payment::create($paymentDetails);

        return null;
    }

    public function updatePayment($paymentId, array $newDetails) 
    {
        return Payment::whereId($paymentId)->update($newDetails);
    }
    
    public function getTotalAmountPayments($date = null)
    {
        if(!empty($date))
        {
            $date = Carbon::parse($date);
            return Payment::selectRaw('SUM(amount) AS total')
                            ->selectRaw('SUM(interest) AS interest')
                            ->whereYear('payment_date', '=', $date->year)
                            ->whereMonth('payment_date', '=', $date->month)
                            ->get(); 
        }

        return Payment::selectRaw('SUM(amount) AS total')
                        ->selectRaw('SUM(interest) AS interest')
                        ->get()
                        ->first(); 
    }

    public function getPaymentByLoan($loanId)
    {
        return Payment::where('loan_id', $loanId)
                    ->get();
        
    }
    public function getPaymentByDate($loanId = null, $date)
    {
        if(!empty($date))
        {
            $date = Carbon::parse($date);
            
            if(checkdate($date->month, $date->day, $date->year) && !empty($loanId))
            {
                return Payment::whereYear('payment_date', '=', $date->year)
                            ->whereMonth('payment_date', '=', $date->month)
                            ->where('loan_id', $loanId)
                            ->get();
            }

            if(checkdate($date->month, $date->day, $date->year))
            {
                return Payment::whereYear('payment_date', '=', $date->year)
                                ->whereMonth('payment_date', '=', $date->month)
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
        return Payment::where('loan_id', $loanId)
                        ->selectRaw('SUM(amount) AS total_paid')
                        ->selectRaw('SUM(interest) AS interest')
                        ->first();                     
    }

    public function getRemaingPaymentByLoan($loanId)
    {
        $this->data = $this->loanRepository->getLoanById($loanId);

        if($this->data)
        {
            $total_paid = $this->getPaymentAmounByLoan($loanId)->total_paid;

            $remaing_payment = $this->data->amount - $total_paid;

            $interest_rate = $this->groupRepository->getGroupById($this->data['type'])->interest_rate;
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
