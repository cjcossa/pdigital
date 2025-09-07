<?php
namespace App\DTO;

use App\Enums\Status;


class RoleData
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $trace_id = null,
        public ?string $description = null,
        public ?string $status = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['rname'] ?? '',
            trace_id: $data['tid'] ?? '',
            description: $data['desc'] ?? '',
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