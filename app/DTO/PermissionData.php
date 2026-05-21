<?php
namespace App\DTO;

use App\Enums\Status;


class PermissionData extends BaseData
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
            name: $data['pname'] ?? '',
            trace_id: $data['tid'] ?? '',
            description: $data['desc'] ?? '',
            status: $data['stus'] ?? Status::ACTIVE->value
        );
    }
}