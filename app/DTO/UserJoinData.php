<?php
namespace App\DTO;

use App\Consts\State;
use App\Enums\Status;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;


class UserJoinData
{
    public function __construct(
        public ?string $id = null,
        public ?string $user_id = null,
        public ?string $group_id = null,
        public ?string $trace_id = null,
        public ?string $role_id = null,
        public ?string $group_cycle_id = null,
        public ?string $status = null,
        public ?string $approved_by = null,
        public ?DateTime $updated_at = null

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            user_id: $data['uid'] ?? '',
            group_id: $data['gid'] ?? '',
            trace_id: $data['tid'] ?? '',
            role_id: $data['rid'] ?? '',
            group_cycle_id: $data['gcid'] ?? '',
            status: $data['stus'] ?? Status::PENDING->value,
            approved_by: $data['aby'] ?? '',
            updated_at:$data['uat'] ?? Carbon::now()
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