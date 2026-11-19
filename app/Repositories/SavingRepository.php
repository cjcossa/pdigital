<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Interfaces\SavingRepositoryInterface;
use App\Models\Saving;
use Carbon\Carbon;
use App\Models\GroupCycle;
use Illuminate\Support\Facades\DB;
use App\Repositories\Concerns\ScopesToGroupAccess;

class SavingRepository implements SavingRepositoryInterface 
{
    use ScopesToGroupAccess;

    public function getAllSavings() 
    {
        $query = Saving::query()
                    ->join('users', 'users.id', '=', 'savings.user_id');
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        return $query
                    ->get([
                        'savings.*',
                        DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                    ]);
    }

    public function getSavingById($savingId) 
    {
        $query = Saving::query();
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        return $query->findOrFail($savingId);
    }

    public function getSavingsByCycle($cycleId)
    {
        $query = Saving::query();
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        return $query->where('group_cycle_id', $cycleId)->get();
    }

    public function deleteSaving($savingId) 
    {
        $this->getSavingById($savingId)->delete();
    }
    public function createSaving(array $savingDetails)
    {
        GroupCycle::where('id', $savingDetails['group_cycle_id'])
                ->where('status', Status::ACTIVE->value)
                ->firstOrFail();

        return Saving::create($savingDetails);
    }

    
    public function updateSaving($savingId, array $newDetails) 
    {
        $saving = $this->getSavingById($savingId);
        $saving->update($newDetails);
        return $saving;
    }

    public function getSavingBalanceByUser($userId)
    {
        $query = Saving::query();
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        return $query->where('user_id', $userId)
                        ->selectRaw('SUM(amount) AS balance')
                        ->selectRaw('SUM(social_amount) AS samount')
                        ->first();                     
    }

    public function getTotalAmountSavings($date = null)
    {
        $query = Saving::query();
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        if(!empty($date))
        {
            $date = Carbon::parse($date);
            return $query->selectRaw('SUM(amount) AS total')
                            ->selectRaw('SUM(social_amount) AS samount')
                            ->whereYear('saving_date', '=', $date->year)
                            ->whereMonth('saving_date', '=', $date->month)
                            ->first(); 
        }

        return $query->selectRaw('SUM(amount) AS total')
                        ->selectRaw('SUM(social_amount) AS samount')
                        ->first(); 
    }

    public function getSavingByUser($userId)
    {
        $query = Saving::query();
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        return $query->where('user_id', $userId)
                        ->get();
    }
    public function getSavingByDate($userId = null, $date)
    {
        $query = Saving::query();
        $this->scopeToAccessibleGroups($query, 'savings.group_id', 'savings.user_id');

        if(!empty($date))
        {
            $date = Carbon::parse($date);
            
            if(checkdate($date->month, $date->day, $date->year) && !empty($userId))
            {
                return $query->whereYear('saving_date', '=', $date->year)
                            ->whereMonth('saving_date', '=', $date->month)
                            ->where('user_id', $userId)
                            ->get();
            }

            if(checkdate($date->month, $date->day, $date->year))
            {
                return $query->whereYear('saving_date', '=', $date->year)
                                ->whereMonth('saving_date', '=', $date->month)
                                ->get();
            }
        }

        if(!empty($userId))
        {
            return $this->getSavingByUser($userId);
        }
        return $query->get();

    }



}
