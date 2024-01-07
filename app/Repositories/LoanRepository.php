<?php

namespace App\Repositories;

use App\Consts\Group;
use App\Consts\State;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\SavingRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LoanRepository implements LoanRepositoryInterface
{
    private SavingRepositoryInterface $savingRepository;
    private UserRepositoryInterface $userRepository;
    private $data;

    public function __construct(SavingRepositoryInterface $savingRepository, UserRepositoryInterface $userRepository
    ) 
    {
        $this->savingRepository = $savingRepository;
        $this->userRepository = $userRepository;
        $this->data = null;
    }

    public function getAllLoans() 
    {
        return Loan::join('users', 'users.id', '=', 'loans.user_id')
                    ->get(['loans.*', 'users.name']);
    }

    public function getLoanById($loanId) 
    {
        return Loan::findOrFail($loanId);
    }

    public function deleteLoan($loanId) 
    {
        Loan::destroy($loanId);
    }

    public function createLoan(array $loanDetails) 
    {
        if($this->getLoanEligibilityByUser($loanDetails['user_id'], $loanDetails))
            return Loan::create($loanDetails);

        return null;
    }

    public function updateLoan($loanId, array $newDetails) 
    {
        return Loan::whereId($loanId)->update($newDetails);
    }
    
    public function getTotalAmountLoans($date = null)
    {
        if(!empty($date))
        {
            $date = Carbon::parse($date);
            return Loan::selectRaw('SUM(amount) AS total')
                            ->whereYear('loan_date', '=', $date->year)
                            ->whereMonth('loan_date', '=', $date->month)
                            ->get(); 
        }

        return Loan::selectRaw('SUM(amount) AS total')
                        ->get()
                        ->first(); 
    }

    public function getLoanByUser($userId, $status = null)
    {
        if($status == null)
        {
            return Loan::where('user_id', $userId)
                        ->get();
        }
        return Loan::where('user_id', $userId)
                    ->where('status', $status)
                    ->get();
    }
    public function getLoanByDate($userId = null, $date)
    {
        if(!empty($date))
        {
            $date = Carbon::parse($date);
            
            if(checkdate($date->month, $date->day, $date->year) && !empty($userId))
            {
                return Loan::whereYear('loan_date', '=', $date->year)
                            ->whereMonth('loan_date', '=', $date->month)
                            ->where('user_id', $userId)
                            ->get();
            }

            if(checkdate($date->month, $date->day, $date->year))
            {
                return Loan::whereYear('loan_date', '=', $date->year)
                                ->whereMonth('loan_date', '=', $date->month)
                                ->get();
            }
        }

        if(!empty($userId))
        {
            return $this->getLoanByUser($userId);
        }
    }

    public function getLoanEligibilityByUser($userId, array $loanDetails)
    {
        $this->data = $this->getLoanByUser($userId, State::L_NPSTUS);

        if(!$this->data->isEmpty())
            return false;

        $this->data = $this->savingRepository->getSavingBalanceByUser($userId);
        
        $this->data->balance *= 2;

        if($this->data->balance >= $loanDetails['amount'])
        {
            return true;
        }

        $this->data = $this->userRepository->getUserById($userId);

        if($this->data->group == Group::G_ETUSR)
        {
            return true;
        }

        return false;
    }
    
    public function getLoansToPay()
    {
        return Loan::join('users', 'users.id', '=', 'loans.user_id')
                    ->where('status', '<>', State::L_PDSTUS)
                    ->get(['loans.*', 'users.name']);
    }
}
