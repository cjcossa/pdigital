<?php
namespace App\DTO;

use App\Consts\State;
use App\Enums\Status;
use Illuminate\Support\Str;


class GroupData
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $join_code = null,
        public ?string $owner_id = null,
        public ?string $image = null,
        public ?string $description = null,
        public ?string $status = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['gname'] ?? '',
            join_code: $data['jcod'] ?? '',
            owner_id: $data['oid'] ?? '',
            image: $data['img'] ?? '',
            description: $data['desc'] ?? '',
            status: $data['status'] ?? Status::ACTIVE->value
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