<?php
namespace App\DTO;

use App\Enums\Status;


class RolePermissionData extends BaseData
{
    public function __construct(
        public ?string $role_id = null,
        public ?string $permission_id = null,
        public ?string $trace_id = null,
        public ?string $status = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            role_id: $data['rid'] ?? '',
            permission_id: $data['pid'] ?? '',
            trace_id: $data['tid'] ?? '',
            status: $data['stus'] ?? Status::ACTIVE->value
        );
    }
}