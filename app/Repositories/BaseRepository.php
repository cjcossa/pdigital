<?php
namespace App\Repositories;

use App\Interfaces\PreUserRepositoryInterface;
use App\DTO\ResponseData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\error;

class BaseRepository
{
    private $_data;
    private bool $_status;
    private string $_message;


    protected function setData($data = null)
    {
        $this->_data = $data;
    }
    protected function setStatus($status = null)
    {
        $this->_status = $status;
    }
    protected function setMessage($message = null)
    {
        $this->_message = $message;
    }

    protected function getData()
    {
        return $this->_data;
    }
    protected function getStatus()
    {
        return $this->_status ?? false;
    }
    protected function getMessage()
    {
        return $this->_message ?? '';
    }

    protected function isData()
    {
        if(sizeof($this->getData()) === 0 )
            return false;

        return true;
    }

    protected function sendResponse()
    {
        if(!$this->getStatus())
        {
            $this->rollBack();
            $this->log();
        }
            
        return new ResponseData(
            success: $this->getStatus(), 
            message: $this->getMessage(),
            data: $this->getData()
        );
    }

    protected function begin()
    {
        DB::beginTransaction();
    }
    protected function commit()
    {
        DB::commit();
    }

    protected function rollBack()
    {
        DB::rollBack();
    }

    protected function log()
    {
        Log::error($this->getMessage());
    }
}