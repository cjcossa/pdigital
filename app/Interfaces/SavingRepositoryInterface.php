<?php

namespace App\Interfaces;

interface SavingRepositoryInterface 
{
    public function getAllSavings();
    public function getSavingById($savingId);
    public function deleteSaving($savingId);
    public function createSaving(array $SavingDetails);
    public function updateSaving($savingId, array $newDetails);
    public function getSavingByUser($savingId);
    public function getTotalAmountSavings($date = null);
    public function getSavingBalanceByUser($usergId);
    public function getSavingByDate($userId = null, $date);  
}
