<?php

namespace App\Repositories;

use App\Consts\Group;
use App\Interfaces\PreUserRepositoryInterface;
use App\Models\PreUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\DTO\PreUserData;
use App\DTO\ResponseData;
use App\Enums\Status;
use Illuminate\Support\Facades\DB;

class PreUserRepository implements PreUserRepositoryInterface
{
    protected $_preUserModel;
    protected $_data;
    protected $_preUserData;

    public function __construct(PreUser $preUserModel)
    {
        $this->_preUserModel = $preUserModel;
        $this->_data = null;
        $this->_preUserData = null;
    }

    public function createPreUser(PreUserData $preUserData)
    {
        try
        {
            DB::beginTransaction();

            $this->_data = $this->_preUserModel->create([
                'first_name' => $preUserData->firstname,
                'last_name' => $preUserData->lastname,
                'pin' => $preUserData->pin,
                'primary_phone' => $preUserData->primaryPhone,
                'phones' => json_encode($preUserData->phones),
                'doc_details' => json_encode($preUserData->docDetails),
                'beneficiaries' => json_encode($preUserData->beneficiaries),
                'trace_id' => $preUserData->trace === '' ? null : $preUserData->trace,
                'status' => $preUserData->status
            ]);

            DB::commit();

            return new ResponseData(
                success: true, 
                message: __('messages.register_success'), 
                data: $this->_data
            );

        }catch(\Exception $e)
        {
            DB::rollBack();
            Log::error('Erro ao registar pre user: '. $e->getMessage());

            return new ResponseData(
                success: false, 
                message: __('messages.register_failed'), 
                data: $this->_data
            );
        } 
    }

    public function getPreUser(PreUserData $preUserData)
    {
        try
        {
            if($preUserData->id)
                $this->getPreUserById($preUserData->id);
            else if ($preUserData->primaryPhone)
                $this->getPreUserByPhone($preUserData->primaryPhone);
            else if($preUserData->status)
                $this->getPreUserByStatus($preUserData->status);

            //return $this->getData();
            
            return new ResponseData(
                success: true, 
                data: $this->getData()
            );                                  

        }catch(\Exception $e)
        {
            Log::error('Erro ao busar pre users: '. $e->getMessage());

            return new ResponseData(
                success: false, 
                data: $this->getData()
            );
        }
    }

    public function updatePreUser(PreUserData $preUserData)
    {
        try
        {

            $this->_data = $this->_preUserModel->where('id', $preUserData->id)
                                                ->update($preUserData->filteredArray());
            return new ResponseData(
                success: true, 
                data: $this->getData()
            );                                  
            
        }catch(\Exception $e)
        {
            Log::error('Erro ao busar pre users: '. $e->getMessage());

            return new ResponseData(
                success: false, 
                data: $this->_data
            );
        }
    }

    private function getPreUserById($id)
    {
        $this->_data = $this->_preUserModel->where('id', $id)
                                            ->first();     
    }

    private function getPreUserByPhone($phone)
    {
        $this->_data = $this->_preUserModel->where('primary_phone', operator: $phone)
                                            ->first();     
    }

    public function getData()
    {
        return $this->_data;
    }

    private function getPreUserByStatus($status, $records = 10)
    {
        $this->_data = $this->_preUserModel->where('status', $status)
                                            ->orderBy('updated_at', 'asc')
                                            ->take($records)
                                            ->get();
    }
}
