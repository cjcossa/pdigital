<?php
namespace App\DTO;

use App\Consts\State;
use App\Enums\Status;
use Illuminate\Support\Str;


class GroupCycleData
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $group_id = null,
        public ?string $trace_id = null,
        public ?string $start_date = null,
        public ?string $end_date = null,
        public ?string $status = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['gcname'] ?? '',
            group_id: $data['gid'] ?? '',
            trace_id: $data['tid'] ?? '',
            start_date: $data['sdate'] ?? '',
            end_date: $data['edate'] ?? '',
            status: $data['stus'] ?? Status::ACTIVE->value
        );
    }

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