<?php

namespace App\Repositories;

use App\Interfaces\SavingRepositoryInterface;
use App\Models\Saving;
use Carbon\Carbon;

class SavingRepository implements SavingRepositoryInterface 
{
    public function getAllSavings() 
    {
        return Saving::join('users', 'users.id', '=', 'savings.user_id')
                    ->get(['savings.*', 'users.name']);
    }

    public function getSavingById($savingId) 
    {
        return Saving::findOrFail($savingId);
    }

    public function deleteSaving($savingId) 
    {
        Saving::destroy($savingId);
    }

    public function createSaving(array $savingDetails) 
    {
        return Saving::create($savingDetails);
    }

    public function updateSaving($savingId, array $newDetails) 
    {
        return Saving::whereId($savingId)->update($newDetails);
    }
    
    public function getSavingBalanceByUser($userId)
    {
        return Saving::where('user_id', $userId)
                        ->selectRaw('SUM(amount) AS balance')
                        ->selectRaw('SUM(social_fund) AS sfund')
                        ->first();                     
    }

    public function getTotalAmountSavings($date = null)
    {
        if(!empty($date))
        {
            $date = Carbon::parse($date);
            return Saving::selectRaw('SUM(amount) AS total')
                            ->selectRaw('SUM(social_fund) AS sfund')
                            ->whereYear('saving_date', '=', $date->year)
                            ->whereMonth('saving_date', '=', $date->month)
                            ->get(); 
        }

        return Saving::selectRaw('SUM(amount) AS total')
                        ->selectRaw('SUM(social_fund) AS sfund')
                        ->get()
                        ->first(); 
    }

    public function getSavingByUser($userId)
    {
        return Saving::where('user_id', $userId)
                        ->get();
    }
    public function getSavingByDate($userId = null, $date)
    {
        if(!empty($date))
        {
            $date = Carbon::parse($date);
            
            if(checkdate($date->month, $date->day, $date->year) && !empty($userId))
            {
                return Saving::whereYear('saving_date', '=', $date->year)
                            ->whereMonth('saving_date', '=', $date->month)
                            ->where('user_id', $userId)
                            ->get();
            }

            if(checkdate($date->month, $date->day, $date->year))
            {
                return Saving::whereYear('saving_date', '=', $date->year)
                                ->whereMonth('saving_date', '=', $date->month)
                                ->get();
            }
        }

        if(!empty($userId))
        {
            return $this->getSavingByUser($userId);
        }
    }
}
