<?php
namespace App\DTO;

abstract class BaseData
{
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), function ($value) {
            return $value !== null && $value != '' && $value != [];
        });
    }

    public function filteredArray() : array 
    {
        $filtered = array_filter((array)$this, function ($value) {
            return $value !== null && $value !== '' && $value !== '[]';
        });
        
        return $filtered;
    }
}