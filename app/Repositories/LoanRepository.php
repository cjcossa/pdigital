<?php

namespace App\Repositories;

use App\Consts\State;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\SavingRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Loan;
use App\Repositories\Concerns\ScopesToGroupAccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanRepository implements LoanRepositoryInterface
{
    use ScopesToGroupAccess;

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
        $query = Loan::query()
                    ->join('users', 'users.id', '=', 'loans.user_id');
        $this->scopeToAccessibleGroups($query, 'loans.group_id', 'loans.user_id');

        return $query
                    ->get([
                        'loans.*',
                        DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                    ]);
    }

    public function getLoanById($loanId) 
    {
        $query = Loan::query();
        $this->scopeToAccessibleGroups($query, 'loans.group_id', 'loans.user_id');

        return $query->findOrFail($loanId);
    }

    public function deleteLoan($loanId) 
    {
        $this->getLoanById($loanId)->delete();
    }

    // public function createLoan(array $loanDetails) 
    // {
    //     if($this->getLoanEligibilityByUser($loanDetails['user_id'], $loanDetails))
    //         return Loan::create($loanDetails);

    //     return null;
    // }

    public function createLoan(array $loanDetails) 
    {
        $eligible = $this->getLoanEligibilityByUser($loanDetails['user_id'], $loanDetails);
        Log::info('Eligibility check', ['eligible' => $eligible]);

        if($eligible)
            return Loan::create($loanDetails);

        Log::warning('User not eligible for loan', ['user_id' => $loanDetails['user_id']]);
        return null;
    }

    public function updateLoan($loanId, array $newDetails) 
    {
        return $this->getLoanById($loanId)->update($newDetails);
    }
    
    public function getTotalAmountLoans($date = null)
    {
        $query = Loan::query();
        $this->scopeToAccessibleGroups($query, 'loans.group_id', 'loans.user_id');

        if(!empty($date))
        {
            $date = Carbon::parse($date);
            return $query->selectRaw('SUM(amount) AS total')
                            ->whereYear('loans.created_at', '=', $date->year)
                            ->whereMonth('loans.created_at', '=', $date->month)
                            ->get(); 
        }

        return $query->selectRaw('SUM(amount) AS total')
                        ->get()
                        ->first(); 
    }

    public function getLoanByUser($userId, $status = null)
    {
        $query = Loan::query();
        $this->scopeToAccessibleGroups($query, 'loans.group_id', 'loans.user_id');

        if($status == null)
        {
            return $query->where('loans.user_id', $userId)
                        ->get();
        }
        return $query->where('loans.user_id', $userId)
                    ->where('loans.status', (string) $status)
                    ->get();
    }
    public function getLoanByDate($userId = null, $date)
    {
        $query = Loan::query();
        $this->scopeToAccessibleGroups($query, 'loans.group_id', 'loans.user_id');

        if(!empty($date))
        {
            $date = Carbon::parse($date);
            
            if(checkdate($date->month, $date->day, $date->year) && !empty($userId))
            {
                return $query->whereYear('loans.created_at', '=', $date->year)
                            ->whereMonth('loans.created_at', '=', $date->month)
                            ->where('loans.user_id', $userId)
                            ->get();
            }

            if(checkdate($date->month, $date->day, $date->year))
            {
                return $query->whereYear('loans.created_at', '=', $date->year)
                                ->whereMonth('loans.created_at', '=', $date->month)
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
        $balance = (float) ($this->data->balance ?? 0);

        if($balance * 2 >= $loanDetails['amount'])
        {
            return true;
        }

        return false;
    }
    
    public function getLoansToPay()
    {
        $query = Loan::query()
                    ->join('users', 'users.id', '=', 'loans.user_id');
        $this->scopeToAccessibleGroups($query, 'loans.group_id', 'loans.user_id');

        return $query
                    ->where('loans.status', '<>', (string) State::L_PDSTUS)
                    ->get([
                        'loans.*',
                        DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                    ]);
    }


    //New things
    
    public function calculateTotalAmount(float $amount, float $interestRate): float
    {
        return $amount + ($amount * $interestRate / 100);
    }

    public function getRemainingAmount(string $loanId): float
    {
        $loan = $this->getLoanById($loanId);

        $paid = app(PaymentRepositoryInterface::class)
                ->getPaymentAmounByLoan($loanId)
                ->total_paid ?? 0;

        return $loan->amount - $paid;
    }

    public function canAcceptPayment(string $loanId, float $amount): bool
    {
        return $amount <= $this->getRemainingAmount($loanId);
    }

    public function updateLoanStatusIfPaid(string $loanId): void
    {
        $remaining = $this->getRemainingAmount($loanId);

        if ($remaining <= 0) {
            $this->updateLoan($loanId, [
                'status' => (string) State::L_PDSTUS
            ]);
        }
    }

}
