<?php

namespace App\Interfaces;

interface LoanRepositoryInterface 
{
    public function getAllLoans();
    public function getLoanById($loanId);
    public function deleteLoan($loanId);
    public function createLoan(array $loanDetails);
    public function updateLoan($loanId, array $newDetails);
    public function getLoanByUser($userId, $status = null);
    public function getTotalAmountLoans($date = null);
    public function getLoanByDate($userId = null, $date);  
    public function getLoanEligibilityByUser($userId, array $loanDetails);
    public function getLoansToPay();
    
}
